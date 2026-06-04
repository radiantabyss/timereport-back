<?php
namespace App\Domains\Company\Member\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;

class DeleteAction extends Action
{
    public function run($id) {
        $item = Model\CompanyMember::find($id);

        if ( !$item ) {
            return Response::error(__('Company Member not found.'));
        }

        $item->delete();

        return Response::success();
    }
}