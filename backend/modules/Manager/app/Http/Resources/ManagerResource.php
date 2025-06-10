<?php

declare(strict_types=1);

namespace Modules\Manager\Http\Resources;

use Illuminate\Http\Request;
use Modules\User\Users\Enums\RolesEnum;
use TiMacDonald\JsonApi\JsonApiResource;

class ManagerResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'manager_id' => $this->manager_id,
            'email' => $this->email,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->fullName(),
            'created_at' => $this->created_at,
            'role' => $this->isSuperManager() ? RolesEnum::SUPER_MANAGER : RolesEnum::MANAGER,
        ];
    }
}
