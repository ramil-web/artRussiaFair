<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static ARTIST()
 * @method static static SCULPTOR()
 * @method static static PHOTOGRAPHER()
 * @method static static GALLERY()
 */
final class ParticipantTypesEnum extends Enum
{
    const ARTIST = 'artist';
    const SCULPTOR = 'sculptor';
    const PHOTOGRAPHER = 'photographer';
    const GALLERY = 'gallery';
}
