<?php

namespace App\Models;

use App\Enums\VerificationStatus;
use Database\Factories\SellerProfileFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Verification fields are set only through {@see self::markAs()} by an admin.
 */
#[Fillable(['nik', 'address', 'village', 'phone'])]
#[Hidden(['nik'])]
class SellerProfile extends Model
{
    /** @use HasFactory<SellerProfileFactory> */
    use HasFactory;

    protected $attributes = [
        'verification_status' => 'pending',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'verification_status' => VerificationStatus::class,
            'verified_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function markAs(VerificationStatus $status, User $admin, ?string $note = null): void
    {
        $this->verification_status = $status;
        $this->verification_note = $note;
        $this->verified_by = $admin->id;
        $this->verified_at = now();
        $this->save();
    }
}
