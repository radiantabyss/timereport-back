<?php
namespace App\Domains\Timereport\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;

class SetInvoiceAction extends Action
{
    public function run() {
        $data = \Request::all();

        Model\Timereport::whereIn('id', $data['ids'])->update([
            'invoice_id' => $data['invoice_id'] ?? null,
        ]);

        return Response::success();
    }
}
