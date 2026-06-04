<?php
namespace App\Domains\Dashboard\Dashboard\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Dashboard\Dashboard\Presenters\Presenter;
use App\Domains\Dashboard\Dashboard\Transformers\Transformer;
use App\Domains\Dashboard\Dashboard\Validators\Validator;

class UpdateAction extends Action
{
    public function run($id) {
        $data = \Request::all();

        //validate request
        $validation = Validator::run($data);
        if ( $validation !== true ) {
            return Response::error($validation);
        }

        $data = Transformer::run($data);
        $item = Model\Dashboard::find($id);
        $item->update($data);
        $item = Presenter::run($item);

        return Response::success(compact('item'));
    }
}
