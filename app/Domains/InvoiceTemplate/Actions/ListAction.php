<?php
namespace App\Domains\InvoiceTemplate\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\InvoiceTemplate\Presenters\ListPresenter;
use App\Domains\InvoiceTemplate\Filters\Filter;

class ListAction extends Action
{
    public function run() {
        //get query
        $query = Model\InvoiceTemplate::where('is_active', true);

        //apply filters
        $filters = \Request::all();
        $filters['order_by'] = $filters['order_by'] ?? 'id';
        $filters['order'] = $filters['order'] ?? 'asc';
        Filter::apply($query, $filters);

        //paginate
        $per_page = \Request::get('per_page') ?: config('settings.data_table_per_page');
        $paginated = $query->paginate($per_page);
        $items = ListPresenter::run($paginated->items());
        $total = $paginated->total();
        $pages = $paginated->lastPage();

        return Response::success(compact('items', 'total', 'pages'));
    }
}
