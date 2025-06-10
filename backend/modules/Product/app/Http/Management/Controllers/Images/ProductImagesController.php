<?php

declare(strict_types=1);

namespace Modules\Product\Http\Management\Controllers\Images;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Product\Http\Management\DTO\Images\ProductImageDto;
use Modules\Product\Http\Management\Requests\ProductEditImageRequest;
use Modules\Product\Http\Management\Requests\ProductImageRequest;
use Modules\Product\Http\Management\Service\Images\ProductImagesService;
use Modules\Product\Models\Product;
use Modules\Product\Storages\ProductImages\Size;

class ProductImagesController extends Controller
{
    public function __construct(
        private ProductImagesService $imagesService,
    ) {}

    /**
     * Store product images (preview and main).
     *
     * Usage place - Admin section.
     */
    public function store(ProductImageRequest $request, Product $product): JsonResponse
    {
        $imagesDto = ProductImageDto::fromRequest($request);

        $size = new Size(
            width: config('product.image.preview.size.width'),
            height: config('product.image.preview.size.height'),
        );

        $this->imagesService->upload($imagesDto, $product, $size);

        return $this->guessResponseMessage($imagesDto);
    }

    /**
     * Edit product images.
     *
     * Usage place - Admin section.
     */
    public function edit(ProductEditImageRequest $request, Product $product): JsonResponse
    {
        $imageDto = ProductImageDto::fromRequest($request);

        $size = new Size(
            width: config('product.image.preview.size.width'),
            height: config('product.image.preview.size.height'),
        );

        $this->imagesService->uploadEdit($imageDto, $product, $size);

        return $this->guessResponseMessage($imageDto);
    }

    private function guessResponseMessage(ProductImageDto $dto): JsonResponse
    {
        if (! $dto->mainImages && $dto->previewImage) {
            return response()->json(
                ['message' => 'Preview image was uploaded. All another images you can upload later.'],
                206,
            );
        }

        if ($dto->mainImages && ! $dto->previewImage) {
            return response()->json(['message' => 'Product images was uploaded. Preview image you can upload later.'],
                206);
        }

        if (! $dto->previewImage && ! $dto->mainImages) {
            return response()->json(
                ['message' => 'Product images was not specified. You can upload it later.'],
                206,
            );
        }

        return response()->json(['message' => 'Images was uploaded successfully.']);
    }
}
