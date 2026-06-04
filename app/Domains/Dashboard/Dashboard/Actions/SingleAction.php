<?php
namespace App\Domains\Dashboard\Dashboard\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Dashboard\Dashboard\Presenters\Presenter;

class SingleAction extends Action
{
    public function run($id) {
        $item = Model\Dashboard::find($id);

        if ( !$item ) {
            return Response::error(\Domain::name().' not found.');
        }

        $item = Presenter::run($item);

        return Response::success(compact('item'));
    }
}
