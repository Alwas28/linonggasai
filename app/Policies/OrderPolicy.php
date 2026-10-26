<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    /**
     * Payment and fulfilment status are confirmed manually by the owning seller only.
     * Another store's order answers 404 so its existence is not revealed.
     */
    public function update(User $user, Order $order): Response
    {
        if ($order->store->user_id !== $user->id) {
            return Response::denyAsNotFound();
        }

        return $user->isApprovedSeller() ? Response::allow() : Response::deny();
    }
}
