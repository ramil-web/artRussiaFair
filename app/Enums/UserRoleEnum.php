<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static SUPER_ADMIN()
 * @method static static CONTENT_MANAGER()
 * @method static static MANAGER()
 * @method static static COMMISSION()
 * @method static static PARTICIPANT()
 * @method static static RESIDENT()
 */
final class UserRoleEnum extends Enum
{
    const  SUPER_ADMIN = 'super_admin';
    const  CONTENT_MANAGER = 'content_manager';
    const  MANAGER = 'manager';
    const  COMMISSION = 'commission';
    const  PARTICIPANT = 'participant';
    const  RESIDENT = 'resident';
}
