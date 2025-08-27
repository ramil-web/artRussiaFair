<?php

namespace App\Exports;

use App\Enums\PersonTypesEnum;
use App\Exceptions\CustomException;
use App\Models\Person;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class EmployeeExport implements FromCollection, WithHeadings, WithStrictNullComparison, WithTitle, WithColumnWidths
{

    public function __construct(public array $dateInterval)
    {}

    /**
     * @return Builder[]|Collection|\Illuminate\Support\Collection
     * @throws CustomException
     */
    public function collection()
    {
        try {
            return Person::query()
                ->where('type', PersonTypesEnum::EMPLOYEE())
                ->whereBetween('created_at', $this->dateInterval)
                ->select(['id', 'user_application_id', 'full_name', 'passport'])
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

    /**
     * @return string[]
     */
    public function headings(): array
    {
        return ['ID сотрудника', 'ID заявки', 'Имя фамилия', 'серия номер паспорта'];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Workers ' . date('Y-m-d_H-i-s');
    }
}
