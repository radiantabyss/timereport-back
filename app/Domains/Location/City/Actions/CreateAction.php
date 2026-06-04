<?php
namespace App\Domains\Location\City\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Location\City\Presenters\Presenter;
use App\Domains\Location\City\Transformers\Transformer;
use App\Domains\Location\City\Validators\Validator;

class CreateAction extends Action
{
    public function run() {
        $data = \Request::all();

        $validation = Validator::run($data);
        if ( $validation !== true ) {
            return Response::error($validation);
        }

        $data = Transformer::run($data);
        $item = Model\City::create($data);
        $item = Presenter::run($item);

        return Response::success(compact('item'));
    }
}