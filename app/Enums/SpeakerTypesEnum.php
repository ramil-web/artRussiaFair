<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static SPEAKER()
 * @method static static PROJECT_TEAM()
 * @method static static CURATOR()
 */
final class SpeakerTypesEnum extends Enum
{
    const SPEAKER = 'speaker';
    const PROJECT_TEAM = 'project_team';
    const CURATOR = 'curator';
}
