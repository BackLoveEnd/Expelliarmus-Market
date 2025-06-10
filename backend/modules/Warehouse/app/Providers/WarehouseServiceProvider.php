<?php

namespace Modules\Warehouse\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Modules\Order\Cart\Services\Cart\ClientCartService;
use Modules\Product\Http\Management\Service\Attributes\Handlers\ProductAttributeService;
use Modules\Warehouse\Http\Controllers\DiscountController;
use Modules\Warehouse\Jobs\CancelExpiredDiscounts;
use Modules\Warehouse\Jobs\WarehouseCombinedProductAvailability;
use Modules\Warehouse\Jobs\WarehouseDefaultProductAvailability;
use Modules\Warehouse\Jobs\WarehouseSingleProductAvailability;
use Modules\Warehouse\Services\Discount\ProductDiscountServiceFactory;
use Modules\Warehouse\Services\Warehouse\WarehouseProductInfoService;
use Modules\Warehouse\Services\Warehouse\WarehouseStockService;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class WarehouseServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Warehouse';

    protected string $nameLower = 'warehouse';

    /**
     * Boot the application events.
     */
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

        $this->app->register(AuthServiceProvider::class);

        $this->app
            ->when(DiscountController::class)
            ->needs(ProductDiscountServiceFactory::class)
            ->give(function (Application $app) {
                return new ProductDiscountServiceFactory($app);
            });

        $this->app
            ->when([ClientCartService::class, WarehouseStockService::class])
            ->needs(WarehouseProductInfoService::class)
            ->give(function (Application $app) {
                return new WarehouseProductInfoService(new ProductAttributeService);
            });
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        // $this->commands([]);
    }

    protected function registerCommandSchedules(): void
    {
        $this->app->booted(function () {
            /** @var Schedule $schedule */
            $schedule = $this->app->make(Schedule::class);

            $schedule->job(new CancelExpiredDiscounts, 'low')->everyMinute();

            $schedule->job(new WarehouseSingleProductAvailability, 'low')->everyThirtySeconds();

            $schedule->job(new WarehouseCombinedProductAvailability, 'low')->everyThirtySeconds();

            $schedule->job(new WarehouseDefaultProductAvailability, 'low')->everyThirtySeconds();
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
}
