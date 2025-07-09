<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        // Fetch all conversations for sidebar
        if ($user->role === 'manager') {
            $conversations = Conversation::where('admin_id', $user->id);
        } elseif ($user->user_type === 'company' && $user->company && $user->company->designation === 'client') {
            $conversations = Conversation::where('client_id', $user->id);
        } elseif ($user->user_type === 'company' && $user->company && $user->company->designation === 'supplier') {
            $conversations = Conversation::where('supplier_id', $user->company->id);
        } else {
            $conversations = Conversation::where('id', 0);
        }
        $conversations = $conversations->with(['messages' => function ($query) {
            $query->latest();
        }, 'client', 'admin', 'supplier'])->latest('last_message_at')->get();

        $conversation = null;
        $messages = null;
        $conversationId = $request->query('conversation');
        if ($conversationId) {
            $conversation = $conversations->where('id', $conversationId)->first();
            if ($conversation) {
                $messages = $conversation->messages()
                    ->with('sender')
                    ->orderBy('created_at', 'asc')
                    ->get(['*']);
                // Mark unread messages as read
                $messages->where('is_read', false)
                    ->where('sender_id', '!=', $user->id)
                    ->each->markAsRead();
            }
        } elseif ($conversations->count() > 0) {
            // Optionally, auto-select the first conversation
            $conversation = $conversations->first();
            $messages = $conversation->messages()
                ->with('sender')
                ->orderBy('created_at', 'asc')
                ->get(['*']);
            $messages->where('is_read', false)
                ->where('sender_id', '!=', $user->id)
                ->each->markAsRead();
        }
        return view('messages.index', compact('conversations', 'conversation', 'messages'));
    }

    public function store(Request $request, Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $request->validate([
            'content' => 'nullable|string|max:1000',
            'image' => 'nullable|file|max:10240', // 10MB limit for images
            'file' => 'nullable|file|max:10240' // 10MB limit for general files
        ]);

        if (!$request->filled('content') && !$request->hasFile('image') && !$request->hasFile('file')) {
            return back()->withErrors(['content' => 'Please enter a message or attach a file.'])->withInput();
        }

        $filePath = null;
        $fileName = null;
        $fileType = null;
        $fileSize = null;
        $imagePath = null;

        // Handle file upload (new system)
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('messages/files', 'public');
            $fileName = $file->getClientOriginalName();
            $fileType = $file->getMimeType();
            $fileSize = $file->getSize();
        }
        // Handle image upload (backward compatibility)
        elseif ($request->hasFile('image')) {
            $file = $request->file('image');
            $imagePath = $file->store('messages', 'public');
            // Also store in new fields for consistency
            $filePath = $imagePath;
            $fileName = $file->getClientOriginalName();
            $fileType = $file->getMimeType();
            $fileSize = $file->getSize();
        }

        $message = $conversation->messages()->create([
            'sender_id' => Auth::id(),
            'content' => $request->content,
            'image' => $imagePath, // Keep for backward compatibility
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_type' => $fileType,
            'file_size' => $fileSize
        ]);

        $conversation->update(['last_message_at' => now()]);

        // Broadcast the new message event
        event(new \App\Events\NewMessage($message));

        if ($request->wantsJson()) {
            return response()->json($message->load('sender'));
        }

        return redirect()->back();
    }

    public function startConversation(Request $request)
    {
        $user = Auth::user();

        // Manager starts conversation with client or supplier
        if ($user->role === 'manager') {
            if ($request->has('client_id')) {
                $request->validate([
                    'client_id' => 'required|exists:users,id',
                    'message' => 'required|string|max:1000'
                ]);
                $client = \App\Models\User::where('id', $request->client_id)
                    ->where('user_type', 'company')
                    ->whereHas('company', function($q){ $q->where('designation', 'client'); })
                    ->first();
                if (!$client) {
                    return redirect()->back()->with('error', 'Selected client is not valid.');
                }
                $existingConversation = Conversation::where('client_id', $request->client_id)
                    ->where('admin_id', $user->id)
                    ->whereNull('supplier_id')
                    ->first();
                if ($existingConversation) {
                    $message = $existingConversation->messages()->create([
                        'sender_id' => $user->id,
                        'content' => $request->message
                    ]);
                    $existingConversation->update(['last_message_at' => now()]);
                    event(new \App\Events\NewMessage($message));
                    return redirect()->route('messages.show', $existingConversation);
                }
                $conversation = Conversation::create([
                    'client_id' => $request->client_id,
                    'admin_id' => $user->id,
                    'status' => 'active'
                ]);
                $message = $conversation->messages()->create([
                    'sender_id' => $user->id,
                    'content' => $request->message
                ]);
                $conversation->update(['last_message_at' => now()]);
                event(new \App\Events\NewMessage($message));
                return redirect()->route('messages.show', $conversation);
            } elseif ($request->has('supplier_id')) {
                $request->validate([
                    'supplier_id' => 'required|exists:suppliers,id',
                    'message' => 'required|string|max:1000'
                ]);
                $supplier = \App\Models\Supplier::find($request->supplier_id);
                if (!$supplier) {
                    return redirect()->back()->with('error', 'Selected supplier is not valid.');
                }
                
                // Use the company_id from the supplier for the conversation
                $companyId = $supplier->company_id;
                if (!$companyId) {
                    return redirect()->back()->with('error', 'Selected supplier does not have an associated company.');
                }
                
                $existingConversation = Conversation::where('supplier_id', $companyId)
                    ->where('admin_id', $user->id)
                    ->whereNull('client_id')
                    ->first();
                if ($existingConversation) {
                    $message = $existingConversation->messages()->create([
                        'sender_id' => $user->id,
                        'content' => $request->message
                    ]);
                    $existingConversation->update(['last_message_at' => now()]);
                    event(new \App\Events\NewMessage($message));
                    return redirect()->route('messages.show', $existingConversation);
                }
                $conversation = Conversation::create([
                    'supplier_id' => $companyId,
                    'admin_id' => $user->id,
                    'status' => 'active'
                ]);
                $message = $conversation->messages()->create([
                    'sender_id' => $user->id,
                    'content' => $request->message
                ]);
                $conversation->update(['last_message_at' => now()]);
                event(new \App\Events\NewMessage($message));
                return redirect()->route('messages.show', $conversation);
            }
        }
        // Client logic
        if ($user->user_type === 'company' && $user->company && $user->company->designation === 'client') {
            $request->validate([
                'admin_id' => 'required|exists:users,id',
                'message' => 'required|string|max:1000'
            ]);
            $admin = \App\Models\User::where('id', $request->admin_id)
                ->where('user_type', 'admin')
                ->where('role', 'admin')
                ->first();
            if (!$admin) {
                return redirect()->back()->with('error', 'Selected admin is not valid.');
            }
            $existingConversation = Conversation::where('client_id', $user->id)
                ->where('admin_id', $request->admin_id)
                ->whereNull('supplier_id')
                ->first();
            if ($existingConversation) {
                $message = $existingConversation->messages()->create([
                    'sender_id' => $user->id,
                    'content' => $request->message
                ]);
                $existingConversation->update(['last_message_at' => now()]);
                event(new \App\Events\NewMessage($message));
                return redirect()->route('messages.show', $existingConversation);
            }
            $conversation = Conversation::create([
                'client_id' => $user->id,
                'admin_id' => $request->admin_id,
                'status' => 'active'
            ]);
            $message = $conversation->messages()->create([
                'sender_id' => $user->id,
                'content' => $request->message
            ]);
            $conversation->update(['last_message_at' => now()]);
            event(new \App\Events\NewMessage($message));
            return redirect()->route('messages.show', $conversation);
        }
        // Supplier logic
        if ($user->user_type === 'company' && $user->company && $user->company->designation === 'supplier') {
            $request->validate([
                'admin_id' => 'required|exists:users,id',
                'message' => 'required|string|max:1000'
            ]);

            $admin = \App\Models\User::where('id', $request->admin_id)
                ->where('user_type', 'admin')
                ->first();

            if (!$admin) {
                return redirect()->back()->with('error', 'Selected admin is not valid.');
            }

            $existingConversation = Conversation::where('supplier_id', $user->company->id)
                ->where('admin_id', $request->admin_id)
                ->whereNull('client_id')
                ->first();

            if ($existingConversation) {
                $message = $existingConversation->messages()->create([
                    'sender_id' => $user->id,
                    'content' => $request->message
                ]);
                $existingConversation->update(['last_message_at' => now()]);
                event(new \App\Events\NewMessage($message));
                return redirect()->route('messages.show', $existingConversation);
            }

            $conversation = Conversation::create([
                'supplier_id' => $user->company->id,
                'admin_id' => $request->admin_id,
                'status' => 'active'
            ]);

            $message = $conversation->messages()->create([
                'sender_id' => $user->id,
                'content' => $request->message
            ]);
            $conversation->update(['last_message_at' => now()]);
            event(new \App\Events\NewMessage($message));
            return redirect()->route('messages.show', $conversation);
        }
        return redirect()->back()->with('error', 'Could not start conversation.');
    }

    public function destroy(Conversation $conversation)
    {
        $this->authorize('view', $conversation);
        try {
            $conversation->messages()->delete();
            $conversation->delete();
            return redirect()->route('messages.index')->with('success', 'Conversation deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('messages.index')->with('error', 'Failed to delete conversation.');
        }
    }

    public function destroyMessage(Message $message)
    {
        $user = Auth::user();
        if ($message->sender_id !== $user->id && $user->user_type !== 'admin') {
            abort(403);
        }
        $message->delete();
        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'Message deleted.');
    }

    public function removeAttachment(Message $message)
    {
        $user = Auth::user();
        if ($message->sender_id !== $user->id && $user->user_type !== 'admin') {
            abort(403);
        }
        if ($message->image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($message->image);
            $message->image = null;
            $message->save();
        }
        if ($message->file_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($message->file_path);
            $message->file_path = null;
            $message->file_name = null;
            $message->file_type = null;
            $message->file_size = null;
            $message->save();
        }
        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'Attachment removed.');
    }

    public function downloadAttachment(Message $message)
    {
        $path = $message->getAttachmentPath();
        $fileName = $message->getAttachmentName();
        
        if (!$path || !\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            abort(404, 'File not found');
        }

        return response()->download(storage_path('app/public/' . $path), $fileName);
    }

    public function show(Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $user = Auth::user();
        
        // Fetch all conversations for the sidebar
        if ($user->user_type === 'admin') {
            $conversations = Conversation::where('admin_id', $user->id);
        } elseif ($user->user_type === 'company' && $user->company && $user->company->designation === 'client') {
            $conversations = Conversation::where('client_id', $user->id);
        } elseif ($user->user_type === 'company' && $user->company && $user->company->designation === 'supplier') {
            $conversations = Conversation::where('supplier_id', $user->company->id);
        } else {
            $conversations = collect();
        }
        $conversations = $conversations->with(['messages' => function ($query) {
            $query->latest();
        }, 'client', 'admin', 'supplier'])->latest('last_message_at')->get();

        // Get messages for the selected conversation
        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark unread messages as read
        $messages->where('is_read', false)
            ->where('sender_id', '!=', $user->id)
            ->each->markAsRead();

        return view('messages.index', compact('conversations', 'conversation', 'messages'));
    }
} 