<?php

namespace App\Policies;

use App\Models\SellerProfile;
use App\Models\User;

class SellerProfilePolicy
{
    /**
     * Admins verify sellers, but never their own registration.
     */
    public function verify(User $user, SellerProfile $sellerProfile): bool
    {
        return $user->isAdmin() && $sellerProfile->user_id !== $user->id;
    }
}
