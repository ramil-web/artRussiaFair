<?php

namespace App\Exports;

use Admin\Services\VipGuestService;
use App\Exceptions\CustomException;
use Illuminate\Database\QueryException;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VipGuestExport implements
    FromArray,
    WithHeadings,
    WithStrictNullComparison,
    WithTitle,
    WithColumnWidths,
    WithStyles
{

    public function __construct(
        public array           $appData,
        public VipGuestService $guestService
    )
    {
    }


    public function headings(): array
    {
        return [
            'ID VIP гостя',
            'ID заявки',
            'Имя фамилия',
            'Организация',
            'Почта',
            'Дата создание',
            'Дата редактирование',
            'Кто заполнил (Участник)'
        ];
    }

    public function title(): string
    {
        return 'VIP-guest ' . date('Y-m-d_H-i-s');
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 20,
            'C' => 20,
            'D' => 20,
            'E' => 30,
            'F' => 20,
            'G' => 25,
            'H' => 30,
        ];
    }

    /**
     * @throws CustomException
     */
    public function array(): array
    {
        try {
            $vipGuests = $this->guestService->list($this->appData)->toArray();
            foreach ($vipGuests as $key => $val) {
                foreach ($val as $k => &$v) {
                    if (in_array($k, ['full_name', 'organization'])) {
                        $vipGuests[$key][$k] = json_encode($v);
                    }
                    if ($k == 'full_name') {
                        if (array_key_exists('ru', $v)) {
                            $vipGuests[$key]['full_name'] = $v['ru'];
                        } elseif (array_key_exists('en', $v)) {
                            $vipGuests[$key]['full_name'] = $v['en'];
                        } else {
                            $vipGuests[$key]['full_name'] = $v;
                        }
                    }
                    if ($k == 'organization') {
                        if (array_key_exists('ru', $v)) {
                            $vipGuests[$key]['organization'] = $v['ru'];
                        } elseif (array_key_exists('en', $v)) {
                            $vipGuests[$key]['organization'] = $v['en'];
                        } else {
                            $vipGuests[$key]['organization'] = $v;
                        }
                    }
                    if ($k == 'user_profile' && !empty($vipGuests[$key]['user_profile'])) {
                        if (array_key_exists('ru', $v['name'])) {
                            $vipGuests[$key]['user_profile'] = $v['name']['ru'];
                        } elseif (array_key_exists('en', $v['name'])) {
                            $vipGuests[$key]['user_profile'] = $v['name']['en'];
                        } else {
                            $vipGuests[$key]['user_profile'] = $v['name'];
                        }

                        if (array_key_exists('ru', $v['surname'])) {
                            $vipGuests[$key]['user_profile'] = $v['surname']['ru'];
                        } elseif (array_key_exists('en', $v['surname'])) {
                            $vipGuests[$key]['user_profile'] = $v['surname']['en'];
                        } else {
                            $vipGuests[$key]['user_profile'] = $v['surname'];
                        }

                        $vipGuests[$key]['user_profile'] = $v['name']['ru'] . ' ' . $v['surname']['ru'];
                        unset($vipGuests[$key]['user_application']);
                    } else if ($k == 'user_application' && empty($vipGuests[$key]['user_profile'])) {
                        if (array_key_exists('ru', $v['representative_name'])) {
                            $vipGuests[$key]['user_profile'] = $v['representative_name']['ru'];
                        } elseif (array_key_exists('en', $v['representative_name'])) {
                            $vipGuests[$key]['user_profile'] = $v['representative_name']['en'];
                        } else {
                            $vipGuests[$key]['user_profile'] = $v['representative_name'];
                        }

                        if (array_key_exists('ru', $v['representative_surname'])) {
                            $vipGuests[$key]['user_profile'] = $v['representative_surname']['ru'];
                        } elseif (array_key_exists('en', $v['representative_surname'])) {
                            $vipGuests[$key]['user_profile'] = $v['representative_surname']['en'];
                        } else {
                            $vipGuests[$key]['user_profile'] = $v['representative_surname'];
                        }
                        $vipGuests[$key]['user_profile'] = $v['representative_name']['ru'] . ' ' . $v['representative_surname']['ru'];
                        unset($vipGuests[$key]['user_application']);
                    }
                }
            }
            return $vipGuests;
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), $e->getCode());
        }
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getStyle('A1:N1')->getFill()->setFillType('solid')->getStartColor()->setARGB('f2e2c2');
        $sheet->getStyle('A1:N1')->getFont()->setBold(true)->setSize(12)->getColor()->setARGB('black');
    }
}
