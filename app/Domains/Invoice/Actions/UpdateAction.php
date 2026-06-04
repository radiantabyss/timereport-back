<?php
namespace App\Domains\Invoice\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Invoice\Presenters\Presenter;
use App\Domains\Invoice\Transformers\Transformer;
use App\Domains\Invoice\Validators\Validator;

class UpdateAction extends Action
{
    public function run($id) {
        $data = \Request::all();

        //validate request
        $validation = Validator::run($data, $id);
        if ( $validation !== true ) {
            return Response::error($validation);
        }

        $item = Model\Invoice::find($id);
        if ( \Gate::denies('delete-invoice', $item) ) {
            return Response::error(\Domain::name().' can\'t be edited anymore.');
        }

        $data = Transformer::run($data, $id);
        $item = Model\Invoice::find($id);
        $item->update($data);
        $item = Presenter::run($item);

        return Response::success(compact('item'));
    }
}
