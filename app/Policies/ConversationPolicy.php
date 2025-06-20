<?php

namespace App\Policies;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConversationPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Conversation $conversation)
    {
        return $user->id === $conversation->client_id || $user->id === $conversation->admin_id || ($conversation->supplier_id && $user->supplier && $user->supplier->id === $conversation->supplier_id);
    }

    public function create(User $user)
    {
        return $user->user_type === 'client' || $user->user_type === 'supplier';
    }

    public function update(User $user, Conversation $conversation)
    {
        return $user->id === $conversation->client_id || $user->id === $conversation->admin_id || ($conversation->supplier_id && $user->supplier && $user->supplier->id === $conversation->supplier_id);
    }
} 