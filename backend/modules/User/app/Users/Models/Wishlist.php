<?php

declare(strict_types=1);

namespace Modules\User\Users\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Product\Models\Product;
use Ramsey\Uuid\Uuid;

/**
 * @property string $id
 * @property int $user_id
 * @property int $product_id
 */
class Wishlist extends Model
{
    public $timestamps = false;

    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'product_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Wishlist $wishlist) {
            if (! $wishlist->id) {
                $wishlist->id = Uuid::uuid7()->toString();
            }
        });
    }
}
