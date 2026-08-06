<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Conversation;
use App\Models\Request as RideRequest;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.request.{conversationId}', function ($user, $conversationId) {
    $conversation = Conversation::find($conversationId);
    if (!$conversation || $conversation->type !== 'request') { // ✅ القيمة الصحيحة هي 'request'
        return false;
    }

    $isUser = get_class($user) === \App\Models\User::class && $user->id === $conversation->user_id;
    $isDriver = get_class($user) === \App\Models\Driver::class && $user->id === $conversation->driver_id;

    return $isUser || $isDriver;
});
Broadcast::channel('chat.support.{conversationId}', function ($user, $conversationId) {
    $conversation = Conversation::find($conversationId);
    if (!$conversation || $conversation->type !== 'support') {
        return false;
    }

    $isOwner = get_class($user) === \App\Models\User::class && $user->id === $conversation->user_id;
    $isAdmin = get_class($user) === \App\Models\User::class && $user->type === 'admin';

    return $isOwner || $isAdmin;
});
Broadcast::channel('tracking.request.{requestId}', function ($user, $requestId) {
    $rideRequest = RideRequest::find($requestId);
    if (!$rideRequest) {
        return false;
    }

    $isUser = get_class($user) === \App\Models\User::class && $user->id === $rideRequest->user_id;
    $isAdmin = get_class($user) === \App\Models\User::class && $user->type === 'admin';

    return $isUser || $isAdmin;
});