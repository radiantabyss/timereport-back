<?php
namespace App\Domains\Location\State\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Location\State\Presenters\Presenter;
use App\Domains\Location\State\Transformers\Transformer;
use App\Domains\Location\State\Validators\Validator;

class CreateAction extends Action
{
    public function run() {
        $data = \Request::all();

        $validation = Validator::run($data);
        if ( $validation !== true ) {
            return Response::error($validation);
        }

        $data = Transformer::run($data);
        $item = Model\State::create($data);
        $item = Presenter::run($item);

        return Response::success(compact('item'));
    }
}