<?php
namespace App\Domains\Timereport\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Timereport\Presenters\Presenter;
use App\Domains\Timereport\Transformers\Transformer;
use App\Domains\Timereport\Validators\Validator;

class UpdateAction extends Action
{
    public function run($id) {
        $data = \Request::all();

        //validate request
        $validation = Validator::run($data, $id);
        if ( $validation !== true ) {
            return Response::error($validation);
        }

        $item = Model\Timereport::find($id);
        if ( \Gate::denies('delete-timereport', $item) ) {
            return Response::error('Entry can\'t be edited anymore.');
        }

        $data = Transformer::run($data, $id);
        $item = Model\Timereport::find($id);
        $item->update($data);
        $item = Presenter::run($item);

        return Response::success(compact('item'));
    }
}
