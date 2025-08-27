<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static WORKER()
 * @method static static EMPLOYEE()
 */
final class PersonTypesEnum extends Enum
{
    const WORKER = 'worker';
    const EMPLOYEE = 'employee';
}
