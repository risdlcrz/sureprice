<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Support\Uploads;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** Managers and administrators are the staff side of a conversation (conversations.admin_id). */
    private function isStaff($user): bool
    {
        return $user->role === 'manager' || $user->user_type === 'admin' || $user->role === 'admin';
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        // Fetch all conversations for sidebar
        if ($this->isStaff($user)) {
            $conversations = Conversation::where('admin_id', $user->id);
        } elseif ($user->isClientCompany()) {
            $conversations = Conversation::where('client_id', $user->id);
        } elseif ($user->isSupplierCompany()) {
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

        // One attachment per message: 'file' (current composer) or 'image' (older clients)
        $file = Uploads::file('file', 'attachments', label: 'The attachment');
        $image = Uploads::file('image', 'images', label: 'The image');
        $request->validate([
            'content' => 'nullable|string|max:1000',
            ...$file->rules,
            ...$image->rules,
        ], $file->messages + $image->messages);

        if (!$request->filled('content') && !$request->hasFile('image') && !$request->hasFile('file')) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Please enter a message or attach a file.'], 422);
            }
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
            $filePath = $file->store('messages', 'public'); // Store directly in messages folder
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

        // Load the sender relationship for JSON response
        $message->load('sender');

        // Broadcast the new message event
        event(new \App\Events\NewMessage($message));

        if ($request->wantsJson()) {
            return response()->json($message);
        }

        return redirect()->back();
    }

    public function startConversation(Request $request)
    {
        $user = Auth::user();

        // A manager or admin starts a conversation with a client or a supplier
        if ($this->isStaff($user)) {
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
        if ($user->isClientCompany()) {
            $request->validate([
                'manager_id' => 'required|exists:users,id',
                'message' => 'required|string|max:1000'
            ]);
            $manager = \App\Models\User::where('id', $request->manager_id)
                ->where(fn ($q) => $q->where('role', 'manager')->orWhere('user_type', 'admin')->orWhere('role', 'admin'))
                ->first();
            if (!$manager) {
                return redirect()->back()->with('error', 'Selected recipient is not valid.');
            }
            $existingConversation = Conversation::where('client_id', $user->id)
                ->where('admin_id', $request->manager_id)
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
                'admin_id' => $request->manager_id,
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
        if ($user->isSupplierCompany()) {
            $request->validate([
                'admin_id' => 'required|exists:users,id',
                'message' => 'required|string|max:1000'
            ]);

            $admin = \App\Models\User::where('id', $request->admin_id)
                ->where(fn ($q) => $q->where('user_type', 'admin')->orWhere('role', 'admin'))
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

    /**
     * Shows an attachment in the browser (chat thumbnails and the image viewer) for conversation participants.
     * Served through the app rather than /storage, so it works without the public/storage symlink.
     * Only raster images are shown inline; anything else (including SVG/HTML) is sent as a download.
     */
    public function showAttachment(Message $message)
    {
        [$file, $fileName] = $this->attachmentFile($message);
        $mime = \Illuminate\Support\Facades\File::mimeType($file) ?: 'application/octet-stream';

        if (!in_array($mime, Message::INLINE_IMAGE_TYPES, true)) {
            return response()->download($file, $fileName);
        }

        return response()->file($file, [
            'Content-Type' => $mime,
            'Content-Disposition' => \Symfony\Component\HttpFoundation\HeaderUtils::makeDisposition('inline', $fileName, \Illuminate\Support\Str::ascii($fileName) ?: 'image'),
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, max-age=86400',
        ]);
    }

    public function downloadAttachment(Message $message)
    {
        [$file, $fileName] = $this->attachmentFile($message);

        return response()->download($file, $fileName);
    }

    /** [absolute path, display name] of a message's attachment, after checking the user is in the conversation. */
    private function attachmentFile(Message $message): array
    {
        $this->authorize('view', $message->conversation);

        $path = $message->getAttachmentPath();
        if (!$path || !Storage::disk('public')->exists($path)) {
            abort(404, 'File not found');
        }

        return [Storage::disk('public')->path($path), $message->getAttachmentName() ?: basename($path)];
    }

    public function show(Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        return redirect()->route('messages.index', ['conversation' => $conversation->id]);
    }
    public function getConversationsUpdate(Request $request)
    {
        $user = Auth::user();
        $conversations = collect();

        if ($user->user_type === 'admin') {
            $conversations = Conversation::with(['client', 'supplier', 'messages' => function($q) {
                $q->latest()->limit(1);
            }])
            ->where('admin_id', $user->id)
            ->distinct()
            ->orderBy('last_message_at', 'desc')
            ->get()
            ->map(function($conversation) {
                $lastMessage = $conversation->messages->first();
                $participantName = $conversation->client ? ($conversation->client->company?->company_name ?? $conversation->client->name ?? 'Unknown') : 
                                 ($conversation->supplier ? $conversation->supplier->company_name : 'Unknown');
                
                return [
                    'id' => $conversation->id,
                    'name' => $participantName,
                    'last_message' => $lastMessage ? ($lastMessage->content ?: 'Attachment sent') : 'No messages yet',
                    'last_message_time' => $lastMessage ? $lastMessage->created_at->diffForHumans() : null,
                    'unread_count' => $conversation->messages()->where('sender_id', '!=', Auth::id())->where('is_read', false)->count(),
                    'url' => route('messages.show', $conversation)
                ];
            });
        } elseif ($user->role === 'manager') {
            $conversations = Conversation::with(['client', 'supplier', 'messages' => function($q) {
                $q->latest()->limit(1);
            }])
            ->where('admin_id', $user->id)
            ->distinct()
            ->orderBy('last_message_at', 'desc')
            ->get()
            ->map(function($conversation) {
                $lastMessage = $conversation->messages->first();
                $participantName = $conversation->client ? ($conversation->client->company?->company_name ?? $conversation->client->name ?? 'Unknown') : 
                                 ($conversation->supplier ? $conversation->supplier->company_name : 'Unknown');
                
                return [
                    'id' => $conversation->id,
                    'name' => $participantName,
                    'last_message' => $lastMessage ? ($lastMessage->content ?: 'Attachment sent') : 'No messages yet',
                    'last_message_time' => $lastMessage ? $lastMessage->created_at->diffForHumans() : null,
                    'unread_count' => $conversation->messages()->where('sender_id', '!=', Auth::id())->where('is_read', false)->count(),
                    'url' => route('messages.show', $conversation)
                ];
            });
        } elseif ($user->user_type === 'company') {
            $conversations = Conversation::with(['admin', 'messages' => function($q) {
                $q->latest()->limit(1);
            }])
            ->where('client_id', $user->id)
            ->distinct()
            ->orderBy('last_message_at', 'desc')
            ->get()
            ->map(function($conversation) {
                $lastMessage = $conversation->messages->first();
                
                return [
                    'id' => $conversation->id,
                    'name' => $conversation->admin->name,
                    'last_message' => $lastMessage ? ($lastMessage->content ?: 'Attachment sent') : 'No messages yet',
                    'last_message_time' => $lastMessage ? $lastMessage->created_at->diffForHumans() : null,
                    'unread_count' => $conversation->messages()->where('sender_id', '!=', Auth::id())->where('is_read', false)->count(),
                    'url' => route('messages.show', $conversation)
                ];
            });
        }

        return response()->json($conversations);
    }
} 