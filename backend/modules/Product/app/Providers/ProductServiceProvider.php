<?php

namespace Modules\Product\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;
use Modules\Product\Http\Management\Contracts\Storage\ProductImagesStorageInterface;
use Modules\Product\Storages\ProductImages\LocalProductImagesStorage;
use Modules\Product\Storages\ProductImages\S3ProductImagesStorage;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ProductServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Product';

    protected string $nameLower = 'product';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerConfig();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));

        if (config('filesystems.provider') === 'file') {
            $this->app->singleton(ProductImagesStorageInterface::class, function (Application $app) {
                return new LocalProductImagesStorage(
                    new ImageManager(Driver::class),
                    Storage::disk('public_products_images'),
                );
            });
        } elseif (config('filesystems.provider') === 's3') {
            $this->app->singleton(ProductImagesStorageInterface::class, function (Application $app) {
                return new S3ProductImagesStorage(
                    new ImageManager(Driver::class),
                    Storage::disk('s3'),
                );
            });
        }
    }

    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(AuthServiceProvider::class);
    }

    protected function registerCommands(): void
    {
        // $this->commands([]);
    }

    protected function registerCommandSchedules(): void
    {
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('inspire')->hourly();
        // });
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
