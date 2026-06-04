<?php
namespace App\Domains\Invoice\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Invoice\Presenters\Presenter;

class SingleAction extends Action
{
    public function run($id) {
        $item = Model\Invoice::with('client', 'company', 'client_company',)->find($id);

        if ( !$item ) {
            return Response::error(\Domain::name().' not found.');
        }

        $item = Presenter::run($item);

        return Response::success(compact('item'));
    }
}
