<?php

namespace App\Exports;

use App\Enums\PersonTypesEnum;
use App\Exceptions\CustomException;
use App\Models\Person;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class WorkerExport implements FromCollection, WithHeadings, WithStrictNullComparison, WithTitle, WithColumnWidths
{

    public function __construct(public array $dateInterval)
    {}
    /**
     * @return Collection
     * @throws CustomException
     */
    public function collection(): Collection
    {
        try {
            return Person::query()
                ->where('type', PersonTypesEnum::WORKER())
                ->select(['id', 'user_application_id', 'full_name', 'passport'])
                ->whereBetween('created_at', $this->dateInterval)
                ->get();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @return int[]
     */
    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 35,
            'C' => 30,
            'D' => 30,
            'E' => 30,
        ];
    }

    public function headings(): array
    {
        return ['ID рабочего', 'ID заявки', 'Имя фамилия', 'серия номер паспорта'];
    }

    public function title(): string
    {
        return 'Workers ' . date('Y-m-d_H-i-s');
    }
}
