<?php

declare(strict_types=1);

namespace Modules\Order\Order\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Order\Order\Models\Coupon;

class CancelExpiredCoupons implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(): void
    {
        Coupon::query()
            ->whereRaw("DATE(expires_at) <= ?", [now()->toDateString()])
            ->delete();
    }
}