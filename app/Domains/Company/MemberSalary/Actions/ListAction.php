<?php
namespace App\Domains\Company\MemberSalary\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Filter;
use RA\Response;
use App\Models as Model;
use App\Domains\Company\MemberSalary\Presenters\ListPresenter;

class ListAction extends Action
{
    public function run() {
        //get query
        $query = Model\CompanyMemberSalary::orderBy('start_date', 'desc');

        //apply filters
        $filters = \Request::all();
        Filter::apply($query, $filters);

        $items = ListPresenter::run($query->get());

        return Response::success(compact('items'));
    }
}
