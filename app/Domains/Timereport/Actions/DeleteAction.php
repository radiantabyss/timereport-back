<?php
namespace App\Domains\Timereport\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;

class DeleteAction extends Action
{
    public function run($id) {
        $item = Model\Timereport::find($id);

        if ( !$item ) {
            return Response::error(\Domain::name().' not found.');
        }

        if ( \Gate::denies('delete-timereport', $item) ) {
            return Response::error('Entry can\'t be deleted anymore.');
        }

        $item->delete();

        return Response::success();
    }
}
