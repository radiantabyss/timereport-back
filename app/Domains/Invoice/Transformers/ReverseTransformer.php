<?php
namespace App\Domains\Invoice\Transformers;

use App\Models as Model;

class ReverseTransformer
{
    public static function run($item) {
        $number = Model\Invoice::max('number');
        $number++;

        $length = strlen((string) $number);
        for ( $i = 0; $i < 4 - $length; $i++ ) {
            $number = '0'.$number;
        }

        $data = [
            'client_id' => $item->client_id,
            'company_id' => $item->company_id,
            'client_company_id' => $item->client_company_id,
            'template_id' => $item->template_id,
            'series' => $item->series,
            'number' => $number,
            'date' => date('Y-m-d'),
            'due_date' => date('Y-m-d'),
            'lines' => $item->lines,
            'vat' => $item->vat,
            'total' => -$item->total,
            'total_with_vat' => -$item->total_with_vat,
            'currency' => $item->currency,
            'conversion_rate' => $item->conversion_rate,
            'conversion_rate_date' => $item->conversion_rate_date,
            'status' => 'reverse',
        ];

        return $data;
    }
}
