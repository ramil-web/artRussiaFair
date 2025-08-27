<?php

namespace Lk\Repositories\VisualizationComment;

use App\Models\VisualizationComment;
use Lk\Repositories\BaseRepository;

class VisualizationCommentRepository extends BaseRepository
{
    public function __construct(VisualizationComment $model)
    {
        parent::__construct($model);
    }
}
