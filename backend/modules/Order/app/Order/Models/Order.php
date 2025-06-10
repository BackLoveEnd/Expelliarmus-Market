<?php

namespace Modules\Order\Order\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\Order\Database\Factories\OrderFactory;
use Modules\Order\Order\Enum\OrderStatusEnum;
use Modules\User\Users\Contracts\UserInterface;

/**
 * @property int $id
 * @property string $order_id
 * @property OrderStatusEnum $status
 * @property float $total_price
 * @property string $contact_email
 * @property string $userable_type
 * @property int $userable_id
 * @property Carbon created_at
 */
class Order extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'status',
        'total_price',
        'created_at',
        'userable_type',
        'userable_id',
        'contact_email',
    ];

    public function userable(): MorphTo
    {
        return $this->morphTo();
    }

    public function orderLines(): HasMany
    {
        return $this->hasMany(OrderLine::class);
    }

    public function scopeClient(Builder $builder, UserInterface $client): Builder
    {
        return $builder
            ->where('userable_type', $client::class)
            ->where('userable_id', $client->id);
    }

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'status' => OrderStatusEnum::class,
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        self::creating(function (Order $order) {
            if ($order->created_at === null) {
                $order->created_at = Carbon::now();
            }

            if ($order->order_id === null) {
                $order->order_id = randomNumber(12);
            }
        });
    }

    protected static function newFactory(): OrderFactory
    {
        return new OrderFactory;
    }
}
