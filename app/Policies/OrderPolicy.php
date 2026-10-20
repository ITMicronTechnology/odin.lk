<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Customers may only see their own orders; admins may see all.
     */
    public function view(User $user, Order $order): bool
    {
        return $user->isAdmin() || $order->user_id === $user->id;
    }

    /**
     * Customers may cancel their own order until it starts processing.
     */
    public function cancel(User $user, Order $order): bool
    {
        return $order->user_id === $user->id && $order->isCancellable();
    }
}
