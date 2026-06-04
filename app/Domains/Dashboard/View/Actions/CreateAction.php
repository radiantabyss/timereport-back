<?php
namespace App\Domains\Dashboard\View\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Dashboard\View\Presenters\Presenter;
use App\Domains\Dashboard\View\Transformers\Transformer;
use App\Domains\Dashboard\View\Validators\Validator;

class CreateAction extends Action
{
    public function run() {
        $data = \Request::all();

        //validate request
        $validation = Validator::run($data);
        if ( $validation !== true ) {
            return Response::error($validation);
        }

        $data = Transformer::run($data);
        $item = Model\DashboardView::create($data);
        $item = Presenter::run($item);

        return Response::success(compact('item'));
    }
}
