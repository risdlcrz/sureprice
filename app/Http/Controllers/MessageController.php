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

    public function index()
    {
        $user = Auth::user();
        
        // Debug logging
        \Log::info('MessageController::index called', [
            'user_id' => $user->id,
            'user_type' => $user->user_type,
            'role' => $user->role,
            'company_designation' => $user->company?->designation ?? 'no company'
        ]);
        
        // Handle different user types
        if ($user->user_type === 'admin') {
            $conversations = Conversation::where('admin_id', $user->id);
        } elseif ($user->user_type === 'company' && $user->company && $user->company->designation === 'client') {
            $conversations = Conversation::where('client_id', $user->id);
        } else {
            // For other user types, return empty conversations
            $conversations = Conversation::where('id', 0); // This will return no results
        }
        
        $conversations = $conversations->with(['messages' => function ($query) {
            $query->latest();
        }, 'client', 'admin'])->latest('last_message_at')->get();

        \Log::info('Conversations found', [
            'count' => $conversations->count(),
            'conversations' => $conversations->pluck('id')->toArray()
        ]);

        return view('messages.index', compact('conversations'));
    }

    public function show(Conversation $conversation)
    {
        $this->authorize('view', $conversation);
        
        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark unread messages as read
        $messages->where('is_read', false)
            ->where('sender_id', '!=', Auth::id())
            ->each->markAsRead();

        return view('messages.show', compact('conversation', 'messages'));
    }

    public function store(Request $request, Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        $message = $conversation->messages()->create([
            'sender_id' => Auth::id(),
            'content' => $request->content
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
        // Only allow clients to start conversations
        $user = Auth::user();
        
        \Log::info('MessageController::startConversation called', [
            'user_id' => $user->id,
            'user_type' => $user->user_type,
            'role' => $user->role,
            'company_designation' => $user->company?->designation ?? 'no company',
            'admin_id' => $request->admin_id,
            'message' => $request->message
        ]);
        
        if ($user->user_type !== 'company' || !$user->company || $user->company->designation !== 'client') {
            \Log::warning('Unauthorized user tried to start conversation', [
                'user_id' => $user->id,
                'user_type' => $user->user_type
            ]);
            return redirect()->back()->with('error', 'Only clients can start new conversations.');
        }

        $request->validate([
            'admin_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000'
        ]);

        // Verify the selected user is an admin
        $admin = \App\Models\User::where('id', $request->admin_id)
            ->where('user_type', 'admin')
            ->where('role', 'admin')
            ->first();

        if (!$admin) {
            \Log::warning('Invalid admin selected', [
                'admin_id' => $request->admin_id
            ]);
            return redirect()->back()->with('error', 'Selected admin is not valid.');
        }

        // Check if conversation already exists
        $existingConversation = Conversation::where('client_id', $user->id)
            ->where('admin_id', $request->admin_id)
            ->first();

        if ($existingConversation) {
            \Log::info('Adding message to existing conversation', [
                'conversation_id' => $existingConversation->id
            ]);
            
            // Add message to existing conversation
            $message = $existingConversation->messages()->create([
                'sender_id' => $user->id,
                'content' => $request->message
            ]);

            $existingConversation->update(['last_message_at' => now()]);
            
            // Broadcast the new message event
            event(new \App\Events\NewMessage($message));

            return redirect()->route('messages.show', $existingConversation);
        }

        \Log::info('Creating new conversation', [
            'client_id' => $user->id,
            'admin_id' => $request->admin_id
        ]);

        // Create new conversation
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

        // Broadcast the new message event
        event(new \App\Events\NewMessage($message));

        \Log::info('New conversation created successfully', [
            'conversation_id' => $conversation->id,
            'message_id' => $message->id
        ]);

        return redirect()->route('messages.show', $conversation);
    }
} 