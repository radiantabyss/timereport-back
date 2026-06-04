<?php
namespace App\Domains\Location\Country\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Location\Country\Filters\Filter;
use App\Domains\Location\Country\Presenters\ListPresenter;

class ListAction extends Action
{
    public function run() {
        //get query
        $query = Model\Country::query();

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