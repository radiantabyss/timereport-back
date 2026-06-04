<?php
namespace App\Domains\Invoice\Transformers;

use App\Models as Model;

class Transformer
{
    public static function run($data, $id = null) {
        $data['series'] = \Auth::user()->team->meta['invoice_series'];
        $data['company_id'] = \Auth::user()->team->meta['company_id'];

        $client = Model\Client::find($data['client_id']);
        $data['client_company_id'] = $client->company_id;

        $total = 0;
        foreach ( $data['lines'] as $line ) {
            $total += $line['quantity'] * str_replace(' ', '', $line['unit_price']);
        }

        $data['total'] = $total;
        $data['total_with_vat'] = $total * (1 + $data['vat'] / 100);
        $data['lines'] = json_encode($data['lines']);

        if ( !$id ) {
            $data['status'] = 'not_paid';
        }

        if ( $data['currency'] == \Auth::user()->company->currency ) {
            $data['conversion_rate'] = null;
            $data['conversion_rate_date'] = null;
        }

        return $data;
    }
}
