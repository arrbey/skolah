<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.presence', function ($user) {
    if (Auth::check()) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => avatarUrl($user)
        ];
    }
});

Broadcast::channel('chat.{receiverId}', function ($user, $receiverId) {
    return (int) $user->id === (int) $receiverId;
});
