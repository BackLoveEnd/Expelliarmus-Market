<?php

declare(strict_types=1);

namespace Modules\Manager\Http\Dto;

use Illuminate\Support\Str;
use Modules\Manager\Http\Requests\ManagerRegisterRequest;

final readonly class ManagerRegisterDto
{
    public string $tmpPassword;

    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $email,
    ) {}

    public static function fromRequest(ManagerRegisterRequest $request): ManagerRegisterDto
    {
        return new self(
            firstName: $request->first_name,
            lastName: $request->last_name,
            email: $request->email,
        );
    }

    public function setTempPassword(): ManagerRegisterDto
    {
        $this->tmpPassword = Str::random(8);

        return $this;
    }
}
