<?php

namespace Lk\Repositories\UserApplication;

use App\Models\UserApplicationImages;
use Illuminate\Database\Eloquent\Model;
use Lk\Repositories\BaseRepository;

class UserApplicationImageRepository extends BaseRepository
{

    public function __construct(UserApplicationImages $model)
    {
        $this->model = $model;
    }

    public function create(array $Data): Model
    {
        return $this->model->updateOrCreate(['url' => $Data['url'], 'user_application_id' => $Data['user_application_id']], $Data);
    }

    public function updateImage(array $Data)
    {
        return $this->model->updateOrCreate(['url' => $Data['url']], $Data);
    }

    public function cleanImage(int $id, array $newImage): void
    {
        $images = $this->model->where('user_application_id', $id)->get()->toArray();
        foreach ($images as $image) {


            if (!in_array($image['url'], $newImage, true)) {
                $this->model->whereId($image['id'])->delete();
            }
        }

    }
}
