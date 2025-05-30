<?php

namespace Modules\Warehouse\Enums;

enum ProductAttributeViewTypeEnum: int
{
    case RADIO_BUTTON = 0;

    case CHECKBOX = 1;

    case DROPDOWN_RADIO = 2;

    case DROPDOWN_CHECKBOX = 3;

    public function toTypes(): string
    {
        return match ($this) {
            self::RADIO_BUTTON => 'radio',
            self::CHECKBOX => 'checkbox',
            self::DROPDOWN_RADIO => 'dropdown.radio',
            self::DROPDOWN_CHECKBOX => 'dropdown.checkbox'
        };
    }
}
