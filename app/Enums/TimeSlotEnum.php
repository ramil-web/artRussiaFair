<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static CHECK_IN()
 * @method static static EXIT()
 */
final class TimeSlotEnum extends Enum
{
    const CHECK_IN = 'check_in';
    const EXIT = 'exit';
}
