<?php
namespace App\Domains\Company\Member\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use RA\Filter;
use App\Models as Model;
use App\Domains\Company\Member\Presenters\ListPresenter;

class ListAction extends Action
{
    public function run() {
        //get query
        $query = Model\CompanyMember::query();

        //apply filters
        $filters = \Request::all();
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
