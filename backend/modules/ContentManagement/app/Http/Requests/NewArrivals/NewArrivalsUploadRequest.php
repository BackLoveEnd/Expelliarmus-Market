<?php

namespace Modules\ContentManagement\Http\Requests\NewArrivals;

use Illuminate\Foundation\Http\FormRequest;
use Modules\ContentManagement\Rules\NewArrivalsExistsRule;
use Modules\ContentManagement\Rules\OnlySpecificDomainRule;
use Modules\ContentManagement\Rules\OnlySpecificStorageUrlRule;

class NewArrivalsUploadRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'arrivals' => ['required', 'array', new NewArrivalsExistsRule()],
            'arrivals.*.file' => ['nullable', 'image', 'required_without:arrivals.*.exists_image_url'],
            'arrivals.*.exists_image_url' => [
                'nullable', 'url', new OnlySpecificStorageUrlRule(url('/storage/content/arrivals'))
            ],
            'arrivals.*.arrival_url' => ['required', 'url', new OnlySpecificDomainRule(config('app.frontend_name'))],
            'arrivals.*.position' => ['required', 'integer', 'between:1,4'],
            'arrivals.*.content' => ['required', 'array'],
            'arrivals.*.content.title' => ['required', 'string', 'max:255'],
            'arrivals.*.content.body' => ['nullable', 'string'],
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
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
