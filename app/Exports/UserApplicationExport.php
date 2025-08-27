<?php

namespace App\Exports;

use Admin\Services\UserApplicationService;
use App\Exceptions\CustomException;
use Illuminate\Database\QueryException;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UserApplicationExport implements
    FromArray,
    WithHeadings,
    WithStrictNullComparison,
    WithTitle,
    WithColumnWidths,
    WithStyles
{

    private const STATUS = [
        'new'                 => 'Новая',
        'approved'            => 'Одобрена',
        'confirmed'           => 'Подтверждена',
        'under_consideration' => 'Доработка',
        'waiting_after_edit'  => 'После правок',
        'rejected'            => 'Отклонена',
        'waiting'             => 'На рассмотрении',
        'processing'          => 'На рассмотрении',
        'pre_assessment'      => 'Предварительная оценка'
    ];

    public function __construct(public array $appData, public UserApplicationService $userApplicationService)
    {
    }

    /**
     * @return int[]
     */
    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 10,
            'C' => 20,
            'D' => 25,
            'E' => 25,
            'F' => 25,
            'G' => 25,
            'H' => 25,
            'I' => 15,
            'J' => 20,
            'K' => 20,
            'L' => 20,
            'M' => 20,
            'N' => 50,
            'O' => 50,
        ];
    }

    public function headings(): array
    {
        return [
            'Идентификатор',
            'Тип',
            'Название галереии',
            'Имя представителя',
            'Город',
            'Почта представителя',
            'Тел. представителя',
            'Статус',
            'Активность',
            'Дата создание',
            'Дата редактирование',
            'Просмотры',
            'Количество визуализации',
            'Оценка визуализации',
            'Оценка заявки',
        ];
    }

    /**
     * @return string
     *
     */
    public function title(): string
    {
        return 'Заявки ' . date('Y-m-d_H-i-s');
    }

    /**
     * @throws CustomException
     */
    public function array(): array
    {
        try {
            $userApps = $this->userApplicationService->list($this->appData)->toArray();
            foreach ($userApps as $key => $app) {
                foreach ($app as $k => &$v) {
                    if (in_array($k, ['visualization_assessment', 'assessment'])) {
                        $userApps[$key][$k] = json_encode($v);
                    }
                    if ($k == 'name_gallery') {
                        if (array_key_exists('ru', $v)) {
                            $userApps[$key]['name_gallery'] = $v['ru'];
                        } elseif (array_key_exists('en', $v)) {
                            $userApps[$key]['name_gallery'] = $v['en'];
                        } else {
                            $userApps[$key]['name_gallery'] = $v;
                        }
                    }
                    if ($k == 'representative_surname') {
                        if (array_key_exists('ru', $v)) {
                            $userApps[$key]['representative_surname'] = $v['ru'];
                        } elseif (array_key_exists('en', $v)) {
                            $userApps[$key]['representative_surname'] = $v['en'];
                        } else {
                            $userApps[$key]['representative_surname'] = $v;
                        }
                    }
                    if ($k == 'representative_city') {
                        if (array_key_exists('ru', $v)) {
                            $userApps[$key]['representative_city'] = $v['ru'];
                        } elseif (array_key_exists('en', $v)) {
                            $userApps[$key]['representative_city'] = $v['en'];
                        } else {
                            $userApps[$key]['representative_city'] = $v;
                        }
                    }
                    if ($k == 'active') {
                        $userApps[$key]['active'] = $v ? 'АКТИВНАЯ' : 'НЕ АКТИВНАЯ';
                    }
                    if ($k == 'status') {
                        $userApps[$key]['status'] = self::STATUS[$v];
                    }
                }
            }
            return $userApps;
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), $e->getCode());
        }
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getStyle('A1:O1')->getFill()->setFillType('solid')->getStartColor()->setARGB('f2e2c2');
        $sheet->getStyle('A1:O1')->getFont()->setBold(true)->setSize(12)->getColor()->setARGB('black');
    }
}
