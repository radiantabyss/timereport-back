<?php
namespace App\Domains\Client\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;

class DeleteAction extends Action
{
    public function run($id) {
        $item = Model\Client::find($id);

        if ( !$item ) {
            return Response::error(\Domain::name().' not found.');
        }

        $item->delete();

        return Response::success();
    }
}
