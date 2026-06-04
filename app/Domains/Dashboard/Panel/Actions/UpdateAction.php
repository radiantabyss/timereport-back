<?php
namespace App\Domains\Dashboard\Panel\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Dashboard\Panel\Presenters\Presenter;
use App\Domains\Dashboard\Panel\Transformers\Transformer;
use App\Domains\Dashboard\Panel\Validators\Validator;

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
        $item = Model\DashboardPanel::find($id);
        $item->update($data);
        $item = Presenter::run($item);

        return Response::success(compact('item'));
    }
}
