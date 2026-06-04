<?php
namespace App\Domains\Client\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Client\Presenters\Presenter;
use App\Domains\Client\Transformers\Transformer;
use App\Domains\Client\Validators\Validator;

class UpdateAction extends Action
{
    public function run($id) {
        $data = \Request::all();

        //validate request
        $validation = Validator::run($data, $id);
        if ( $validation !== true ) {
            return Response::error($validation);
        }

        $data = Transformer::run($data, $id);
        $item = Model\Client::find($id);
        $item->update($data);
        $item = Presenter::run($item);

        return Response::success(compact('item'));
    }
}
