<?php
namespace App\Domains\Location\State\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;

class DeleteAction extends Action
{
    public function run($id) {
        $item = Model\State::find($id);

        if ( !$item ) {
            return Response::error(__('Location State not found.'));
        }

        $item->delete();

        return Response::success();
    }
}