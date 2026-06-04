<?php
namespace App\Domains\Supplier\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Supplier\Presenters\Presenter;
use App\Domains\Supplier\Transformers\Transformer;
use App\Domains\Supplier\Validators\Validator;

class CreateAction extends Action
{
    public function run() {
        $data = \Request::all();

        $validation = Validator::run($data);
        if ( $validation !== true ) {
            return Response::error($validation);
        }

        $data = Transformer::run($data);
        $item = Model\Supplier::create($data);
        $item = Presenter::run($item);

        return Response::success(compact('item'));
    }
}