<?php

declare(strict_types=1);

namespace Modules\User\Users\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use TiMacDonald\JsonApi\JsonApiResource;

class GuestResource extends JsonApiResource
{
    public function toAttributes(Request $request)
    {
        $attributes = [
            'guest_id' => Str::mask($this->guest_id, '*', -12),
            'email' => $this->email,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->fullName(),
            'created_at' => $this->created_at->format('Y-m-d H:i').' '.Carbon::now()->timezone,
        ];

        if ($this->phone_number) {
            $attributes['phone_mask'] = Str::mask($this->phone_number, '*', -4);
            $attributes['phone_original'] = $this->phone_number;
        }

        return $attributes;
    }

    public function toId(Request $request): string
    {
        return $this->guest_id;
    }
}
