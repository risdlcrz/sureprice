<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $conversations = Conversation::where('user_one_id', $user->id)
            ->orWhere('user_two_id', $user->id)
            ->with(['userOne', 'userTwo', 'messages' => function ($query) {
                $query->latest()->take(1);
            }])
            ->get()
            ->map(function ($conversation) use ($user) {
                $otherUser = $conversation->getOtherUser($user);
                $lastMessage = $conversation->messages->first();
                
                return [
                    'id' => $conversation->id,
                    'other_user' => [
                        'id' => $otherUser->id,
                        'name' => $otherUser->name,
                        'avatar' => $otherUser->avatar,
                        'is_online' => $otherUser->is_online,
                    ],
                    'last_message' => $lastMessage ? [
                        'message' => $lastMessage->message,
                        'created_at' => $lastMessage->created_at,
                        'status' => $lastMessage->status,
                    ] : null,
                ];
            });

        return view('chat.index', compact('conversations'));
    }

    public function show(Conversation $conversation)
    {
        $user = Auth::user();
        $otherUser = $conversation->getOtherUser($user);
        
        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read
        $conversation->messages()
            ->where('sender_id', '!=', $user->id)
            ->where('status', '!=', 'read')
            ->update([
                'status' => 'read',
                'read_at' => now()
            ]);

        return view('chat.show', compact('conversation', 'messages', 'otherUser'));
    }

    public function store(Request $request, Conversation $conversation)
    {
        $request->validate([
            'message' => 'required|string',
            'attachment' => 'nullable|file|max:10240' // 10MB max
        ]);

        $message = new Message([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'message' => $request->message,
            'status' => 'sent'
        ]);

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('chat-attachments');
            $message->attachment_path = $path;
            $message->attachment_type = $file->getClientMimeType();
        }

        $message->save();

        // Broadcast the new message event
        broadcast(new NewMessageEvent($message))->toOthers();

        return response()->json($message->load('sender'));
    }

    public function markAsRead(Message $message)
    {
        if ($message->sender_id !== Auth::id()) {
            $message->update([
                'status' => 'read',
                'read_at' => now()
            ]);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Return all users (suppliers/clients) for AJAX search.
     */
    public function users(Request $request)
    {
        $q = $request->input('q', '');
        // Get companies (clients/suppliers)
        $companies = Company::query()
            ->whereIn('designation', ['supplier', 'client'])
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('company_name', 'like', "%$q%")
                         ->orWhere('contact_person', 'like', "%$q%")
                         ->orWhere('email', 'like', "%$q%") ;
                });
            })
            ->orderBy('company_name')
            ->limit(20)
            ->get();

        // Get admins
        $admins = \App\Models\User::query()
            ->where(function($q2) {
                $q2->where('user_type', 'admin')->orWhere('role', 'admin');
            })
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%$q%")
                         ->orWhere('email', 'like', "%$q%")
                         ->orWhere('username', 'like', "%$q%") ;
                });
            })
            ->orderBy('name')
            ->limit(10)
            ->get();

        $results = collect();
        foreach ($admins as $admin) {
            $results->push([
                'id' => $admin->id,
                'name' => $admin->name,
                'type' => 'Admin',
            ]);
        }
        foreach ($companies as $company) {
            $results->push([
                'id' => $company->user_id,
                'name' => $company->company_name,
                'type' => ucfirst($company->designation),
            ]);
        }
        return response()->json($results->values());
    }

    /**
     * Start or get a conversation with a selected user.
     */
    public function start($userId, Request $request)
    {
        $authId = auth()->id();
        if ($authId == $userId) {
            return redirect()->route('chat.index', $request->only('popup'));
        }
        $conversation = \App\Models\Conversation::where(function($q) use ($authId, $userId) {
            $q->where('user_one_id', $authId)->where('user_two_id', $userId);
        })->orWhere(function($q) use ($authId, $userId) {
            $q->where('user_one_id', $userId)->where('user_two_id', $authId);
        })->first();

        if (!$conversation) {
            $conversation = \App\Models\Conversation::create([
                'user_one_id' => $authId,
                'user_two_id' => $userId,
                'type' => 'private',
            ]);
        }
        $params = $request->only('popup');
        return redirect()->route('chat.show', array_merge([$conversation->id], $params));
    }
} 