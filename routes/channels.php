<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('orders', function (User $user): bool {
    return in_array($user->role, ['admin', 'kasir'], true);
});
