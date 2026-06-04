<?php
namespace App\Domains\Invoice\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Invoice\Presenters\ListPresenter;
use App\Domains\Invoice\Filters\Filter;

class ListAction extends Action
{
    public function run() {
        //get query
        $query = Model\Invoice::with('company', 'client');

        //apply filters
        $filters = \Request::all();
        $filters['order_by'] = $filters['order_by'] ?? 'number';
        $filters['order'] = $filters['order'] ?? 'desc';

        if ( \Auth::user()->type == 'client' ) {
            $filters['client_id'] = \Auth::user()->client_id;
        }

        Filter::apply($query, $filters);

        //export results
        if ( isset($filters['export']) && $filters['export'] ) {
            $this->export($query);
        }

        //paginate
        $per_page = \Request::get('per_page') ?: config('settings.data_table_per_page');
        $paginated = $query->paginate($per_page);
        $items = ListPresenter::run($paginated->items());
        $total = $paginated->total();
        $pages = $paginated->lastPage();

        $totals = $this->totals($items);

        return Response::success(compact('items', 'total', 'pages', 'totals'));
    }

    private function totals($items) {
        $totals = [];

        foreach ( $items as $item ) {
            if ( !isset($totals[$item->currency]) ) {
                $totals[$item->currency] = [
                    'without_vat' => 0,
                    'with_vat' => 0,
                ];
            }

            $totals[$item->currency]['without_vat'] += $item->total;
            $totals[$item->currency]['with_vat'] += $item->total_with_vat;
        }

        return $totals;
    }

    private function export($query) {
        $items = ListPresenter::run($query->get());

        $headers = [
            'ID', 'Number', 'Date', 'Status', 'Client',
            'Total', 'Total with VAT', 'Total Received', 'Currency',
        ];

        $values = $items->map(function($item) {
            return [
                $item->id,
                $item->number,
                $item->date,
                $item->status,
                $item->client->name,
                $item->total,
                $item->total_with_vat,
                $item->total_received,
                $item->currency,
            ];
        });

        export_to_csv($headers, $values, __(\Domain::name()).' '.($filters['window'] ?? ''));
    }
}
