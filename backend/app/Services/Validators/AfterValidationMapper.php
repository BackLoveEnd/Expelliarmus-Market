<?php

declare(strict_types=1);

namespace App\Services\Validators;

use Illuminate\Validation\Validator;

trait AfterValidationMapper
{
    protected function getValidatorInstance()
    {
        return parent::getValidatorInstance()->after(function (Validator $validator) {
            $this->processAfterValidation($validator);
        });
    }

    protected function processAfterValidation(Validator $validator): void
    {
        $data = $validator->getData();

        $this->mapDataTypes($data);

        $validator->setData($data);
    }

    abstract public function mapDataTypes(array &$data): void;
}
