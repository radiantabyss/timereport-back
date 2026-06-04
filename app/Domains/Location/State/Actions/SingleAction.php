<?php
namespace App\Domains\Location\State\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Location\State\Presenter;

class SingleAction extends Action
{
    public function run($id) {
        $item = Model\State::find($id);

        if ( !$item ) {
            return Response::error(__('Location State not found.'));
        }

        $item = Presenter::run($item);

        return Response::success(compact('item'));
    }
}