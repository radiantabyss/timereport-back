<?php
namespace App\Domains\Company\Tax\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;

class DeleteAction extends Action
{
    public function run($id) {
        $item = Model\CompanyTax::find($id);

        if ( !$item ) {
            return Response::error(__('Company Tax not found.'));
        }

        $item->delete();

        return Response::success();
    }
}