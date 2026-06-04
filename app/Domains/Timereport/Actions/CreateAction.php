<?php
namespace App\Domains\Timereport\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Timereport\Presenters\Presenter;
use App\Domains\Timereport\Transformers\Transformer;
use App\Domains\Timereport\Validators\Validator;

class CreateAction extends Action
{
    public function run() {
        $data = \Request::all();

        $validation = Validator::run($data);
        if ( $validation !== true ) {
            return Response::error($validation);
        }

        $data = Transformer::run($data);
        $item = Model\Timereport::create($data);
        $item = Presenter::run($item);

        return Response::success(compact('item'));
    }
}
