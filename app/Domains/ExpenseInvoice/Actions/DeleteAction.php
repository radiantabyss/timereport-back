<?php
namespace App\Domains\ExpenseInvoice\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;

class DeleteAction extends Action
{
    public function run($id) {
        $item = Model\ExpenseInvoice::find($id);

        if ( !$item ) {
            return Response::error(__('ExpenseInvoice not found.'));
        }

        $item->delete();

        unlink(config('path.uploads_path').$item->path);

        return Response::success();
    }
}
