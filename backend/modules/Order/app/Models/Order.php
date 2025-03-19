<?php

namespace Modules\Order\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\Order\Database\Factory\OrderFactory;

/**
 * @property Carbon created_at
 */
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'total_price',
        'created_at'
    ];

    public function userable(): MorphTo
    {
        return $this->morphTo();
    }

    public function orderLines(): HasMany
    {
        return $this->hasMany(OrderLine::class);
    }

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime'
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        self::creating(function (Order $order) {
            if ($order->created_at === null) {
                $order->created_at = Carbon::now();
            }
        });
    }

    protected static function newFactory(): OrderFactory
    {
        return new OrderFactory();
    }
}
