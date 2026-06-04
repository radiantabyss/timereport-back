<?php
namespace App\Domains\InvoiceTemplate\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\InvoiceTemplate\Presenters\Presenter;
use App\Domains\InvoiceTemplate\Transformers\Transformer;
use App\Domains\InvoiceTemplate\Validators\Validator;

class UpdateAction extends Action
{
    public function run($id) {
        $data = \Request::all();

        //validate request
        $validation = Validator::run($data, $id);
        if ( $validation !== true ) {
            return Response::error($validation);
        }

        $data = Transformer::run($data, $id);

        //inactivate current template for invoice consistency
        Model\InvoiceTemplate::where('id', $id)->update([
            'is_active' => false,
        ]);

        //create a new template
        $item = Model\InvoiceTemplate::create($data);

        //update clients
        Model\Client::where('default_invoice_template_id', $id)->update([
            'default_invoice_template_id' => $item->id,
        ]);

        $item = Presenter::run($item);

        return Response::success(compact('item'));
    }
}
