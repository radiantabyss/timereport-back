<?php
namespace App\Domains\Client\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Client\Presenters\Presenter;

class SingleAction extends Action
{
    public function run($id) {
        $item = Model\Client::with('company', 'default_invoice_template')->find($id);

        if ( !$item ) {
            return Response::error(\Domain::name().' not found.');
        }

        $item = Presenter::run($item);

        return Response::success(compact('item'));
    }
}
