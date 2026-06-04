<?php
namespace App\Domains\InvoiceTemplate\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;

class DeleteAction extends Action
{
    public function run($id) {
        $item = Model\InvoiceTemplate::find($id);

        if ( !$item ) {
            return Response::error(\Domain::name().' not found.');
        }

        //check if it's used
        $count = Model\Invoice::where('template_id', $id)->count();
        if ( $count ) {
            return Response::e
        }

        $item->delete();

        return Response::success();
    }
}
