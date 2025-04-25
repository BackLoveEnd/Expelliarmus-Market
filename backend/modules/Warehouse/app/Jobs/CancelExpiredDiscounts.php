<?php

namespace Modules\Warehouse\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Modules\Warehouse\Enums\DiscountStatusEnum;
use Modules\Warehouse\Models\Discount;

class CancelExpiredDiscounts implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(): void
    {
        DB::transaction(static function () {
            Discount::query()
                ->chunkById(100, function ($discounts) {
                    foreach ($discounts as $discount) {
                        $discount->update(['status' => DiscountStatusEnum::defineStatus($discount)->value]);
                    }
                });
        });
    }
}
