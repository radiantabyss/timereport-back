<?php
namespace App\Domains\Invoice\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Invoice\Transformers\StatusTransformer;
use App\Domains\Invoice\Validators\StatusValidator;
use App\Domains\Invoice\Presenters\Presenter;

class SetStatusAction extends Action
{
    public function run($id) {
        $data = \Request::all();

        //validate request
        $validation = StatusValidator::run($data, $id);
        if ( $validation !== true ) {
            return Response::error($validation);
        }

        $data = StatusTransformer::run($data);
        $item = Model\Invoice::with('company', 'client')->find($id);
        $item->update($data);
        $item = Presenter::run($item);

        return Response::success(compact('item'));
    }
}
