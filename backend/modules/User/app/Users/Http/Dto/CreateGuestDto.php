<?php

declare(strict_types=1);

namespace Modules\User\Users\Http\Dto;

use App\Services\Validators\JsonApiFormRequest;

final readonly class CreateGuestDto
{
    public function __construct(
        public string $email,
        public string $firstName,
        public string $lastName,
        public string $phone,
        public string $address,
    ) {}

    public static function fromRequest(JsonApiFormRequest $request): CreateGuestDto
    {
        return new self(
            email: $request->email,
            firstName: $request->first_name,
            lastName: $request->last_name,
            phone: $request->phone,
            address: $request->address,
        );
    }
}
