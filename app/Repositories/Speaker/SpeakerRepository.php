<?php

namespace App\Repositories\Speaker;

use App\Models\Speaker;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;

class SpeakerRepository extends BaseRepository
{

    const DESC = 'desc';

    public function __construct(Speaker $model)
    {
        parent::__construct($model);
    }

    /**
     * @param int $id
     * @return Model|null
     */
    public function show(int $id): ?Model
    {
        return $this->findById($id);
    }
}
