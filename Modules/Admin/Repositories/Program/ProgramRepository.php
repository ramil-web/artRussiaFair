<?php

namespace Admin\Repositories\Program;

use Admin\Repositories\BaseRepository;
use App\Exceptions\CustomException;
use App\Models\Program;
use DB;
use Illuminate\Database\QueryException;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;

class ProgramRepository extends BaseRepository
{
    public function __construct(Program $model)
    {
        parent::__construct($model);
    }

    /**
     * @param mixed $id
     * @param mixed $Data
     * @return bool
     */
    public function updateProgram(int $id, mixed $Data): bool
    {
        $model = $this->model->find($id);
        return $model->update($Data);
    }

    /**
     * @param mixed $Data
     * @return mixed
     */
    public function creatProgram(mixed $Data): mixed
    {
        $this->jsonEncode($Data);
        return $this->model->create($Data);
    }

    /**
     * @param array $Data
     * @param int $modelId
     * @return bool
     */
    public function createProgramSpeaker(array $Data, int $modelId): bool
    {
        $data = [];
        foreach ($Data['speaker_id'] as $speakerId) {
            $data[] = ['program_id' => $modelId, 'speaker_id' => $speakerId];
        }
        return DB::table('program_speaker')
            ->insert($data);
    }

    /**
     * @param array $Data
     * @param int $modelId
     * @return bool
     */
    public function createProgramPartner(array $Data, int $modelId): bool
    {
        $data = [];
        foreach ($Data['partners_id'] as $partnerId) {
            $data[] = ['program_id' => $modelId, 'partner_id' => $partnerId];
        }
        return DB::table('program_partners')
            ->insert($data);
    }


    /**
     * @param int $modelId
     * @param array $Data
     * @return bool|null
     */
    public function updateProgramSpeaker(int $modelId, array $Data): ?bool
    {
        $data = [];
        $updated = null;
        if (!empty($Data['speaker_id'])) {
            foreach ($Data['speaker_id'] as $speakerId) {
                $model = DB::table('program_speaker');
                $model->where('program_id', $modelId)
                    ->delete();
                $data[] = ['program_id' => $modelId, 'speaker_id' => $speakerId];
            }
            $updated = DB::table('program_speaker')
                ->insert($data);
        }
        return $updated;
    }


    /**
     * @param int $modelId
     * @param array $Data
     * @return bool|null
     */
    public function updateProgramPartner(int $modelId, array $Data): ?bool
    {
        $data = [];
        $updated = null;
        if (!empty($Data['partners_id'])) {
            foreach ($Data['partners_id'] as $partnerId) {
                $model = DB::table('program_partners');
                $model->where('program_id', $modelId)
                    ->delete();
                $data[] = ['program_id' => $modelId, 'partner_id' => $partnerId];
            }
            $updated = DB::table('program_partners')
                ->insert($data);
        }
        return $updated;
    }


    private function jsonEncode(mixed $Data)
    {
        if (array_key_exists('name', $Data)) {
            $Data['name'] = json_encode($Data['name']);
        }
        if (array_key_exists('moderator_name', $Data)) {
            $Data['moderator_name'] = json_encode($Data['moderator_name']);
        }
        if (array_key_exists('moderator_description', $Data)) {
            $Data['moderator_description'] = json_encode($Data['moderator_description']);
        }
        return $Data;
    }

    /**
     * @param int $id
     * @return bool|mixed|null
     * @throws CustomException
     */
    public function archive(int $id): mixed
    {
        try {
            $query = QueryBuilder::for($this->model);
            return $query
                ->findOrFail($id)
                ->delete();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

}
