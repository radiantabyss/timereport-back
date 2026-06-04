<?php
namespace App\Domains\Dashboard\View\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Dashboard\View\Presenters\Presenter;
use App\Domains\Dashboard\View\Transformers\PatchTransformer;
use App\Domains\Dashboard\View\Validators\PatchValidator;

class PatchAction extends Action
{
    public function run($id) {
        $data = \Request::all();

        //validate request
        $validation = PatchValidator::run($data);
        if ( $validation !== true ) {
            return Response::error($validation);
        }

        $data = PatchTransformer::run($data);
        $item = Model\DashboardView::find($id);
        $item->update($data);
        $item = Presenter::run($item);

        return Response::success(compact('item'));
    }
}
