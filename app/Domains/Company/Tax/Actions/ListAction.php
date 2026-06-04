<?php
namespace App\Domains\Company\Tax\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Company\Tax\Filters\Filter;
use App\Domains\Company\Tax\Presenters\ListPresenter;

class ListAction extends Action
{
    public function run() {
        //get query
        $query = Model\CompanyTax::orderBy('start_date', 'desc');

        //apply filters
        $filters = \Request::all();
        Filter::apply($query, $filters);

        $items = ListPresenter::run($query->get());

        return Response::success(compact('items'));
    }
}
