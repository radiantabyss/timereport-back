<?php
namespace App\Domains\Project\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Project\Presenters\Presenter;
use App\Domains\Project\Transformers\Transformer;
use App\Domains\Project\Validators\Validator;

class CreateAction extends Action
{
    public function run() {
        $data = \Request::all();

        $validation = Validator::run($data);
        if ( $validation !== true ) {
            return Response::error($validation);
        }

        $data = Transformer::run($data);
        $item = Model\Project::create($data);
        $item = Presenter::run($item);

        return Response::success(compact('item'));
    }
}
