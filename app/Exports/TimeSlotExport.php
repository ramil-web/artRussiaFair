<?php

namespace App\Exports;

use App\Exceptions\CustomException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class TimeSlotExport implements FromCollection, WithHeadings, WithStrictNullComparison, WithTitle, WithColumnWidths
{

    use Exportable;

    /**
     * @return Collection
     * @throws CustomException
     */
    public function collection(): Collection
    {
        try {
            return DB::table('orders as o')
                ->leftJoin('user_applications as uap', 'uap.id', 'o.user_application_id')
                ->leftJoin('users as u', 'u.id', 'uap.user_id')
                ->rightJoin('time_slot_start as ts', 'ts.id', 'o.time_slot_start_id')
                ->select([
                    'ts.id as slot_id',
                    'ts.date',
                    'ts.interval_times',
                    'ts.count',
                    'ts.action',
                    'ts.status',
                    'o.id as order_id',
                    'u.username',
                    'u.email'
                ])
                ->orderBy('ts.id')
                ->get();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }

    public function headings(): array
    {
        return ['ID слота', 'Дата', 'Интервал', 'Участники', 'Заезд/выезд', 'Статус', 'ID заказа', 'Кем занят', 'Почта того, кто занял'];
    }

    public function title(): string
    {
        return 'Тайм слоты ' . date('Y-m-d_H-i-s');
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 20,
            'C' => 15,
            'D' => 15,
            'E' => 20,
            'F' => 15,
            'G' => 15,
            'H' => 20,
            'I' => 30,
        ];
    }
}
