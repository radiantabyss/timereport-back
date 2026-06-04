<?php
namespace App\Domains\Dashboard\View\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use RA\Filter;
use App\Models as Model;
use App\Domains\Dashboard\View\Presenters\ListPresenter;

class ListAction extends Action
{
    public function run() {
        //get query
        $query = Model\DashboardView::query();

        //apply filters
        $filters = \Request::all();
        Filter::apply($query, $filters);

        $items = ListPresenter::run($query->get());

        return Response::success(compact('items'));
    }
}
