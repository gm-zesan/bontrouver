<?php

use App\Models\Conversation;
use App\Models\SupportConversation;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    $conversation = Conversation::find($conversationId);
    if (!$conversation) {
        return false;
    }
    return (int) $user->id === (int) $conversation->buyer_id || (int) $user->id === (int) $conversation->seller_id;
});

Broadcast::channel('support.conversation.{conversationId}', function ($user, $conversationId) {
    $conversation = SupportConversation::find($conversationId);
    if (!$conversation) {
        return false;
    }
    return (int) $user->id === (int) $conversation->user_id || $user->isAdmin() || $user->isModerator();
});

Broadcast::channel('admin.support', function ($user) {
    return $user->isAdmin() || $user->isModerator();
});

