<?php
namespace App\Domains\Company\MemberAssociate\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Company\MemberAssociate\Presenters\EditPresenter;

class EditAction extends Action
{
    public function run($id) {
        $item = Model\CompanyMemberAssociate::find($id);

        if ( !$item ) {
            return Response::error(__('Company Member Associate not found.'));
        }

        $item = EditPresenter::run($item);

        return Response::success(compact('item'));
    }
}