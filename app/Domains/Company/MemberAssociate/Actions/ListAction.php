<?php
namespace App\Domains\Company\MemberAssociate\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Filter;
use RA\Response;
use App\Models as Model;
use App\Domains\Company\MemberAssociate\Presenters\ListPresenter;

class ListAction extends Action
{
    public function run() {
        //get query
        $query = Model\CompanyMemberAssociate::orderBy('start_date', 'desc');

        //apply filters
        $filters = \Request::all();
        Filter::apply($query, $filters);

        $items = ListPresenter::run($query->get());

        return Response::success(compact('items'));
    }
}
