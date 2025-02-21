<?php

declare(strict_types=1);

namespace Modules\User\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class UserResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'user_id' => $this->user_id,
            'email' => $this->email,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->last_name.' '.$this->first_name,
            'has2FA' => (bool)$this->two_factor_secret
        ];
    }
}