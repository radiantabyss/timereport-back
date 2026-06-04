<?php
namespace App\Domains\Statement\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Statement\Presenters\Presenter;
use App\Domains\Statement\Transformers\PatchTransformer;
use App\Domains\Statement\Validators\PatchValidator;

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
        $item = Model\Statement::find($id);
        $item->update($data);
        $item = Presenter::run($item);

        return Response::success(compact('item'));
    }
}
