<?php
namespace App\Domains\Location\Country\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Location\Country\Presenter;

class SingleAction extends Action
{
    public function run($id) {
        $item = Model\Country::find($id);

        if ( !$item ) {
            return Response::error(__('Location Country not found.'));
        }

        $item = Presenter::run($item);

        return Response::success(compact('item'));
    }
}