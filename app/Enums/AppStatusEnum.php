<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static NEW()
 * @method static static PRE_ASSESSMENT()
 * @method static static WAITING()
 * @method static static CONSIDERATION()
 * @method static static WAITING_AFTER_EDIT()
 * @method static static CONFIRMED()
 * @method static static REFUSED()
 * @method static static PROCESSING()
 * @method static static REJECTED()
 */
final class AppStatusEnum extends Enum
{
    const  NEW = 'new';
    const PRE_ASSESSMENT = 'pre_assessment';
    const WAITING = 'waiting';
    const  CONSIDERATION = 'under_consideration';
    const  WAITING_AFTER_EDIT = 'waiting_after_edit';
    const  CONFIRMED = 'confirmed';
    const  REJECTED = 'rejected';
    const  PROCESSING = 'processing';

}
