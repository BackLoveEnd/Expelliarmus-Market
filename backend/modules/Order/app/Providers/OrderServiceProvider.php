<?php

namespace Modules\Order\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\ServiceProvider;
use Modules\Order\Cart\Services\Cart\AddingPossibilityProductToCartCheckerService;
use Modules\Order\Cart\Services\Cart\CartDataPrepareService;
use Modules\Order\Cart\Services\Cart\CartStorageService;
use Modules\Order\Cart\Services\Cart\ClientCartService;
use Modules\Order\Cart\Services\Cart\DiscountCartService;
use Modules\User\Coupons\Jobs\CancelExpiredCoupons;
use Modules\User\Coupons\Jobs\SendCouponToUserJob;
use Modules\Warehouse\Services\Warehouse\WarehouseStockService;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class OrderServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Order';

    protected string $nameLower = 'order';

    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerConfig();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
    }

    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);

        $this->app->register(RouteServiceProvider::class);

        $this->registerServicesForCart();
    }

    protected function registerCommands(): void
    {
        // $this->commands([]);
    }

    protected function registerCommandSchedules(): void
    {
        $this->app->booted(function () {
            /** @var Schedule $schedule */
            $schedule = $this->app->make(Schedule::class);

            $schedule->job(new SendCouponToUserJob, 'low')->twiceDaily('0', '10');

            $schedule->job(new CancelExpiredCoupons, 'low')->dailyAt('00:00');
        });
    }

    protected function registerConfig(): void
    {
        $relativeConfigPath = config('modules.paths.generator.config.path');
        $configPath = module_path($this->name, $relativeConfigPath);

        if (is_dir($configPath)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($configPath));

            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $relativePath = str_replace($configPath.DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $configKey = $this->nameLower.'.'.str_replace([DIRECTORY_SEPARATOR, '.php'],
                        ['.', ''],
                        $relativePath);
                    $key = ($relativePath === 'config.php') ? $this->nameLower : $configKey;

                    $this->publishes([$file->getPathname() => config_path($relativePath)], 'config');
                    $this->mergeConfigFrom($file->getPathname(), $key);
                }
            }
        }
    }

    public function provides(): array
    {
        return [];
    }

    protected function registerServicesForCart(): void
    {
        $this->app
            ->when(CartStorageService::class)
            ->needs(Session::class)
            ->give(fn ($app) => $app->make(Session::class));

        $this->app
            ->when([
                AddingPossibilityProductToCartCheckerService::class,
                CartDataPrepareService::class,
            ])
            ->needs(WarehouseStockService::class)
            ->give(fn ($app) => $app->make(WarehouseStockService::class));

        $this->app
            ->when([
                ClientCartService::class,
                CartDataPrepareService::class,
            ])
            ->needs(DiscountCartService::class)
            ->give(fn ($app) => $app->make(DiscountCartService::class));
    }
}
