<?php

namespace Modules\Warehouse\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Warehouse\Models\Discount;

class CancelExpiredDiscounts implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(): void
    {
        Discount::query()
            ->whereDate('end_date', '<', now())
            ->update(['is_cancelled' => true]);
    }
}
