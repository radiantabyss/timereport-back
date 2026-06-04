<?php
namespace App\Domains\Dashboard\Dashboard\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Dashboard\Dashboard\Filters\Filter;
use App\Domains\Dashboard\Dashboard\Presenters\ListPresenter;

class ListAction extends Action
{
    public function run() {
        //get query
        $query = Model\Dashboard::query();

        //apply filters
        $filters = \Request::all();
        Filter::apply($query, $filters);

        //paginate
        $per_page = \Request::get('per_page') ?: config('settings.data_table_per_page');
        $paginated = $query->paginate($per_page);
        $items = ListPresenter::run($paginated->items());
        $total = $paginated->total();
        $pages = $paginated->lastPage();

        $min_position = Model\Dashboard::min('position');
        $max_position = Model\Dashboard::max('position');

        return Response::success(compact('items', 'total', 'pages', 'min_position', 'max_position'));
    }
}
