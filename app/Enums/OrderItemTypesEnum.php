<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static HARDWARE()
 * @method static ADDITIONAL_SERVICE()
 */
final class OrderItemTypesEnum extends Enum
{
    const HARDWARE = 'hardware';
    const ADDITIONAL_SERVICE = 'additional_service';
}
