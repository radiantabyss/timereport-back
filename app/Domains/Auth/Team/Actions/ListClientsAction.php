<?php
namespace App\Domains\Auth\Team\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use RA\Filter;
use App\Models as Model;
use App\Domains\Auth\Team\Presenters\ListClientsPresenter;

class ListClientsAction extends Action
{
    public function run()
    {
        $client_ids = pluck(Model\Client::all());

        $query = Model\User::select('user.*', 'client.name as client_name')
            ->leftJoin('client', 'client.id', '=', 'user.client_id')
            ->whereIn('client.id', $client_ids);

        //apply filters
        $filters = \Request::all();
        Filter::apply($query, $filters);

        //paginate
        $paginated = $query->paginate(config('settings.data_table_per_page'));
        $items = ListClientsPresenter::run($paginated->items());
        $total = $paginated->total();
        $pages = $paginated->lastPage();

        return Response::success(compact('items', 'total', 'pages'));
    }
}
