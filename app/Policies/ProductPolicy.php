<?php

namespace App\Policies;

use App\Enums\ProductStatus;
use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProductPolicy
{
    public function create(User $user): bool
    {
        return $user->isApprovedSeller();
    }

    /**
     * Sellers may only touch products of their own store. Another seller's
     * product answers 404 so its existence is not revealed.
     */
    public function update(User $user, Product $product): Response
    {
        if ($product->store->user_id !== $user->id) {
            return Response::denyAsNotFound();
        }

        return $user->isApprovedSeller() ? Response::allow() : Response::deny();
    }

    public function delete(User $user, Product $product): Response
    {
        return $this->update($user, $product);
    }

    /**
     * Only admins moderate, and never a product from their own store.
     */
    public function moderate(User $user, Product $product): bool
    {
        return $user->isAdmin() && $product->store->user_id !== $user->id;
    }

    public function approve(User $user, Product $product): bool
    {
        return $this->moderate($user, $product)
            && in_array($product->status, [ProductStatus::Pending, ProductStatus::Rejected], true);
    }
}
