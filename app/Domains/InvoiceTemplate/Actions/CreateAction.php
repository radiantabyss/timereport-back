<?php
namespace App\Domains\InvoiceTemplate\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\InvoiceTemplate\Presenters\Presenter;
use App\Domains\InvoiceTemplate\Transformers\Transformer;
use App\Domains\InvoiceTemplate\Validators\Validator;

class CreateAction extends Action
{
    public function run() {
        $data = \Request::all();

        $validation = Validator::run($data);
        if ( $validation !== true ) {
            return Response::error($validation);
        }

        $data = Transformer::run($data);
        $item = Model\InvoiceTemplate::create($data);
        $item = Presenter::run($item);

        return Response::success(compact('item'));
    }
}
