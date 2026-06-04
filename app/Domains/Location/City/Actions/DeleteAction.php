<?php
namespace App\Domains\Location\City\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;

class DeleteAction extends Action
{
    public function run($id) {
        $item = Model\City::find($id);

        if ( !$item ) {
            return Response::error(__('Location City not found.'));
        }

        $item->delete();

        return Response::success();
    }
}