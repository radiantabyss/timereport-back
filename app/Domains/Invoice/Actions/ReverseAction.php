<?php
namespace App\Domains\Invoice\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Invoice\Transformers\ReverseTransformer;

class ReverseAction extends Action
{
    public function run($id) {
        $item = Model\Invoice::with('company', 'client')->find($id);

        if ( !$item ) {
            return Response::error(\Domain::name().' not found.');
        }

        if ( \Gate::denies('reverse-invoice', $item) ) {
            return Response::error(\Domain::name().' can\'t be deleted anymore.');
        }

        //create a new reverse invoice
        $data = ReverseTransformer::run($item);
        Model\Invoice::create($data);

        //set status to reversed
        Model\Invoice::where('id', $id)->update([
            'status' => 'reversed',
        ]);

        return Response::success(compact('item'));
    }
}
