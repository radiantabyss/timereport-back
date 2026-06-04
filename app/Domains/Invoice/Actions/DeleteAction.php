<?php
namespace App\Domains\Invoice\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;

class DeleteAction extends Action
{
    public function run($id) {
        $item = Model\Invoice::find($id);

        if ( !$item ) {
            return Response::error(\Domain::name().' not found.');
        }

        if ( \Gate::denies('delete-invoice', $item) ) {
            return Response::error(\Domain::name().' can\'t be deleted.');
        }

        $item->delete();

        return Response::success();
    }
}
