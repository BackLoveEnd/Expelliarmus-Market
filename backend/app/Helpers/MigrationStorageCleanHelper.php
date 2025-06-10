<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class MigrationStorageCleanHelper
{
    public static function cleanProductStorage(): void
    {
        $directories = Storage::disk('public_products_images')->directories();

        foreach ($directories as $directory) {
            if (preg_match('/^product-id-\d+-images$/', $directory)) {
                Storage::disk('public_products_images')->deleteDirectory($directory);
            }
        }
    }

    public static function cleanSliderContentStorage(): void
    {
        $files = Storage::disk('public_content')->files('slider');

        $except = ['.gitignore', '.gitkeep', config('contentmanagement.default.slider.image')];

        foreach ($files as $file) {
            if (! in_array(str_replace('slider/', '', $file), $except, true)) {
                Storage::disk('public_content')->delete($file);
            }
        }
    }

    public static function cleanArrivalsContentStorage(): void
    {
        $files = Storage::disk('public_content')->files('arrivals');

        $except = ['.gitignore', '.gitkeep'];

        foreach ($files as $file) {
            if (! in_array(str_replace('arrivals/', '', $file), $except, true)) {
                Storage::disk('public_content')->delete($file);
            }
        }
    }

    public static function cleanBrandsLogo(): void
    {
        $files = Storage::disk('public_brands_images')->files();

        $except = ['.gitignore', '.gitkeep', config('brand.default_logo')];

        foreach ($files as $file) {
            if (! in_array($file, $except, true)) {
                Storage::disk('public_brands_images')->delete($file);
            }
        }
    }
}
