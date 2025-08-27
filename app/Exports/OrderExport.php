<?php

namespace App\Exports;

use App\Exceptions\CustomException;
use DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class OrderExport implements FromCollection, WithHeadings, WithStrictNullComparison, WithTitle, WithColumnWidths
{

    use Exportable;

    public function __construct(
        public array        $dateInterval
    )
    {}

    /**
     * @return Collection
     * @throws CustomException
     */
    public function collection(): Collection
    {
        try {
            $locale = app()->getLocale();
            $response = DB::table('orders as o')
                ->leftJoin('order_items as oi', 'oi.order_id', 'o.id')
                ->leftJoin('products as p', 'oi.product_id', 'p.id')
                ->leftJoin('service_catalogs as sc', 'oi.service_catalog_id', 'sc.id')
                ->select(
                    'o.id as orderId',
                    'oi.type as type',
                    'oi.quantity',
                    'sc.name as additionalServiceName',
                    'p.name as hardwareName',
                    'sc.price as additionalServicePrice',
                    'p.price as hardwarePrice',
                )
                ->whereBetween('o.created_at', $this->dateInterval)
                ->orderBy('o.id')
                ->get()
                ->whereNotNull('type');

            foreach ($response as $key => $val) {
                if (!is_null($val->additionalServiceName)) {
                    $response[$key]->additionalServiceName = json_decode($response[$key]->additionalServiceName, true)[$locale];
                    $response[$key]->price = $response[$key]->additionalServicePrice;
                    $response[$key]->name = $response[$key]->additionalServiceName;
                }
                unset($response[$key]->additionalServicePrice);
                unset($response[$key]->additionalServiceName);


                if (!is_null($val->hardwareName)) {
                    $response[$key]->hardwareName = json_decode($response[$key]->hardwareName, true)[$locale];
                    $response[$key]->price = $response[$key]->hardwarePrice;
                    $response[$key]->name = $response[$key]->hardwareName;
                }
                unset($response[$key]->hardwarePrice);
                unset($response[$key]->hardwareName);
            }
            return $response;
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @return string[]
     */
    public function headings(): array
    {
        return ['ID Заказа', 'Тип, доп. услуга/Оборудование', 'Количество', 'Стоимость', 'Наименование'];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Заказы ' . date('Y-m-d_H-i-s');
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
}
