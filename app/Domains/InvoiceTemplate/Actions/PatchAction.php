<?php
namespace App\Domains\InvoiceTemplate\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Client\Presenters\Presenter;
use App\Domains\Client\Transformers\PatchTransformer;
use App\Domains\Client\Validators\PatchValidator;

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
        $item = Model\InvoiceTemplate::find($id);
        $item->update($data);
        $item = Presenter::run($item);

        return Response::success(compact('item'));
    }
}
