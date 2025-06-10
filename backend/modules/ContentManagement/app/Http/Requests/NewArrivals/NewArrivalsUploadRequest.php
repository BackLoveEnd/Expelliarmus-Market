<?php

namespace Modules\ContentManagement\Http\Requests\NewArrivals;

use Illuminate\Foundation\Http\FormRequest;
use Modules\ContentManagement\Models\NewArrival;
use Modules\ContentManagement\Rules\NewArrivalsExistsRule;
use Modules\ContentManagement\Rules\OnlySpecificDomainRule;
use Modules\ContentManagement\Rules\OnlySpecificStorageUrlRule;
use Modules\User\Users\Enums\RolesEnum;

class NewArrivalsUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user(RolesEnum::MANAGER->toString())?->can('manage', NewArrival::class);
    }

    public function rules(): array
    {
        return [
            'arrivals' => ['required', 'array', new NewArrivalsExistsRule],
            'arrivals.*.file' => ['nullable', 'image', 'required_without:arrivals.*.exists_image_url'],
            'arrivals.*.exists_image_url' => [
                'nullable',
                'url',
                new OnlySpecificStorageUrlRule(config('app.url').'/storage/content/arrivals'),
            ],
            'arrivals.*.arrival_url' => ['required', 'url', new OnlySpecificDomainRule(config('app.frontend_url'))],
            'arrivals.*.position' => ['required', 'integer', 'between:1,4'],
            'arrivals.*.content' => ['required', 'array'],
            'arrivals.*.content.title' => ['required', 'string', 'max:255'],
            'arrivals.*.content.body' => ['nullable', 'string'],
            'arrivals.*.content.color' => ['nullable', 'hex_color'],
        ];
    }

    public function attributes(): array
    {
        return [
            'arrivals.*.file' => 'file',
            'arrivals.*.exists_image_url' => 'exists image url',
            'arrivals.*.arrival_url' => 'arrival url',
            'arrivals.*.position' => 'position',
            'arrivals.*.content.*.title' => 'title',
            'arrivals.*.content.*.body' => 'body',
            'arrivals.*.content.color' => 'color',
        ];
    }
}
