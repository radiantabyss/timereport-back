<?php
namespace App\Domains\Location\State\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Location\State\Presenters\Presenter;
use App\Domains\Location\State\Transformers\PatchTransformer;
use App\Domains\Location\State\Validators\PatchValidator;

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
        $item = Model\State::find($id);
        $item->update($data);
        $item = Presenter::run($item);

        return Response::success(compact('item'));
    }
}
