<?php

declare(strict_types=1);

namespace Modules\Statistics\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Product\Models\Product;
use Modules\Statistics\Http\Stats\TotalProductsStats;
use Modules\Statistics\Http\Stats\TotalUsersStats;
use Modules\Statistics\Services\CountStatisticsService;
use Modules\User\Users\Models\Guest;
use Modules\User\Users\Models\User;

class ManagerDefaultStatsController extends Controller
{
    public function __construct(private CountStatisticsService $service) {}

    /**
     * Count and retrieve statistics for general management home page.
     *
     * Usage place - Admin section.
     */
    public function __invoke(): JsonResponse
    {
        $result = $this->service
            ->for([Product::class, User::class, Guest::class])
            ->apply([
                User::class => new TotalUsersStats,
                Guest::class => new TotalUsersStats,
                Product::class => new TotalProductsStats,
            ]);

        return response()->json([
            'data' => [
                'type' => 'statistics',
                'attributes' => [
                    'total_products' => $result['product:total-products'],
                    'total_users' => $result['user:total-users'] + $result['guest:total-users'],
                ],
            ],
        ]);
    }
}
