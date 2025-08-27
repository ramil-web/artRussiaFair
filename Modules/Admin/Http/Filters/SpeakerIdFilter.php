<?php

namespace Admin\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class SpeakerIdFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): void
    {
        $query->whereIn('programs.id', function ($subQuery) use ($value) {
            $subQuery->select('program_speaker.program_id')
                ->from('program_speaker')
                ->where('program_speaker.speaker_id', $value);
        });
    }
}
