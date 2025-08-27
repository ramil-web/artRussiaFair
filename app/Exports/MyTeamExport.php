<?php

namespace App\Exports;

use Admin\Services\MyTeamService;
use App\Exceptions\CustomException;
use Illuminate\Database\QueryException;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Component\HttpFoundation\Response;

class MyTeamExport implements
    FromArray,
    WithHeadings,
    WithStrictNullComparison,
    WithTitle,
    WithColumnWidths,
    WithStyles
{

    public function __construct(
        public MyTeamService $service
    )
    {
    }

    /**
     * @throws CustomException
     */
    public function array(): array
    {
        try {
            $data = [
                'sort'     => 'my_teams.id',
                'per_page' => 'nullable',
                'page'     => '',
            ];
            $teams = $this->service->list($data)->toArray();
            $response = [];
            foreach ($teams as $key => $val) {
                $response[$key]['id'] = $val->id;
                $response[$key]['user_application_id'] = $val->user_application_id;
                $response[$key]['square'] = $val->square;
                $response[$key]['check_in'] = $val->check_in[0]['date'] . ' ' . $val->check_in[0]['interval_times'];
                $response[$key]['exit'] = $val->exit[0]['date'] . ' ' . $val->exit[0]['interval_times'];
                $response[$key]['stand_representative'] = $this->getStandRepresentatives($val);
                $response[$key]['builders'] = $this->getBuilders($val);
                $response[$key]['user_profile'] = $this->getUserProfile($val);
                $response[$key]['created_at'] = $val->created_at;
                $response[$key]['updated_at'] = $val->updated_at;
            }
            return $response;
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 10,
            'C' => 15,
            'D' => 10,
            'E' => 10,
            'F' => 35,
            'G' => 35,
            'H' => 25,
            'I' => 20,
            'J' => 20,
        ];
    }

    /**
     * @return string[]
     */
    public function headings(): array
    {
        return [
            'ID команды',
            'ID заявки',
            'Площадь стенда',
            'Въезд',
            'Выезд',
            'Застройщики',
            'Представители стенда',
            'ФИО, кто заполнил',
            'Дата создание',
            'Дата редактирование',
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getStyle('A1:N1')->getFill()->setFillType('solid')->getStartColor()->setARGB('f2e2c2');
        $sheet->getStyle('A1:N1')->getFont()->setBold(true)->setSize(12)->getColor()->setARGB('black');
    }

    public function title(): string
    {
        return 'My-teams ' . date('Y-m-d_H-i-s');
    }

    /**
     * @param mixed $val
     * @return string
     */
    private function getStandRepresentatives(mixed $val): string
    {
        $result = '';
        if (!empty($val->stand_representatives)) {
            foreach ($val->stand_representatives as $k => $v) {
                if ($v['id']) {
                    $result .= $k + 1 . ') ФИО: ' . $v['full_name'] . ', Паспорт: ' . $v['passport'] . "\n";
                }
            }
        }
        return $result;
    }

    /**
     * @param mixed $val
     * @return string
     */
    private function getBuilders(mixed $val): string
    {
        $result = '';
        if (!empty($val->builders)) {
            foreach ($val->builders as $k => $v) {
                if ($v['id']) {
                    $result .= $k + 1 . ') ФИО: ' . $v['full_name'] . ', Паспорт: ' . $v['passport'] . "\n";
                }
            }
        }
        return $result;
    }

    /**
     * @param mixed $val
     * @return string
     */
    private function getUserProfile(mixed $val): string
    {
        $result = '';
        foreach ($val->user_profile as $val) {
            if (!is_null($val['name'])) {
                $result .= $val['name']['ru'] . ' ' . $val['surname']['ru'];
            }
        }
        return $result;
    }
}
