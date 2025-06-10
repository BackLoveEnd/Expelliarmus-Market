<?php

declare(strict_types=1);

namespace Modules\Product\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;

class ProductImagesStorageUrlRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null) {
            return;
        }
        dd($value);
        $fileSystem = config('filesystems.provider');

        if ($fileSystem === 'file') {
            if (! Str::startsWith($value, config('app.url').'/storage/products')) {
                $fail('Exists image url is not related to expelliarmus.com services.');
            }
        } else {
            if ($fileSystem === 's3') {
                $bucket = config('filesystems.disks.s3.bucket');
                $region = config('filesystems.disks.s3.region');
                $expectedDomains = [
                    "https://{$bucket}.s3.{$region}.amazonaws.com",
                    "https://s3.{$region}.amazonaws.com/{$bucket}",
                ];

                foreach ($expectedDomains as $domain) {
                    if (! Str::startsWith($value, $domain)) {
                        $fail('Exists image url is not related to expelliarmus.com services.');
                    }
                }
            }
        }
    }
}
