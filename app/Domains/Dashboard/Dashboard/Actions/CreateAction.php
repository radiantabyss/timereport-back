<?php
namespace App\Domains\Dashboard\Dashboard\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Dashboard\Dashboard\Presenters\Presenter;
use App\Domains\Dashboard\Dashboard\Transformers\Transformer;
use App\Domains\Dashboard\Dashboard\Validators\Validator;

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
        $item = Model\Dashboard::create($data);
        $item = Presenter::run($item);

        //create a default view
        Model\DashboardView::create([
            'dashboard_id' => $item->id,
            'name' => 'Default View',
            'position' => 1,
        ]);

        return Response::success(compact('item'));
    }
}
