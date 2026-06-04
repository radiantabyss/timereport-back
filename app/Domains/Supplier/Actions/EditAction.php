<?php
namespace App\Domains\Supplier\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Supplier\Presenters\EditPresenter;

class EditAction extends Action
{
    public function run($id) {
        $item = Model\Supplier::find($id);

        if ( !$item ) {
            return Response::error(__('Supplier not found.'));
        }

        $item = EditPresenter::run($item);

        return Response::success(compact('item'));
    }
}