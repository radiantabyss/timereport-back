<?php
namespace App\Domains\InvoiceTemplate\Presenters;

use App\Models as Model;

class ListPresenter
{
    public static function run($items) {
        $invoices = keyBy(Model\Invoice::select(\DB::raw('COUNT(*) as count'), 'template_id')
            ->whereIn('template_id', pluck($items))
            ->groupBy('template_id')
            ->get(), 'template_id');

        $clients = keyBy(Model\Client::select(\DB::raw('COUNT(*) as count'), 'default_invoice_template_id')
            ->whereIn('default_invoice_template_id', pluck($items))
            ->groupBy('default_invoice_template_id')
            ->get(), 'default_invoice_template_id');

        foreach ( $items as $item ) {
            $item->invoices = $invoices[$item->id]->count ?? 0;
            $item->clients = $clients[$item->id]->count ?? 0;
        }

        return $items;
    }
}
