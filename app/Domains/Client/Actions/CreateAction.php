<?php
namespace App\Domains\Client\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Client\Presenters\Presenter;
use App\Domains\Client\Transformers\Transformer;
use App\Domains\Client\Validators\Validator;

class CreateAction extends Action
{
    public function run() {
        $data = \Request::all();

        $validation = Validator::run($data);
        if ( $validation !== true ) {
            return Response::error($validation);
        }

        $data = Transformer::run($data);
        $item = Model\Client::create($data);
        $item = Presenter::run($item);

        return Response::success(compact('item'));
    }
}
