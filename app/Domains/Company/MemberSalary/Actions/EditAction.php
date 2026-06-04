<?php
namespace App\Domains\Company\MemberSalary\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Company\MemberSalary\Presenters\EditPresenter;

class EditAction extends Action
{
    public function run($id) {
        $item = Model\CompanyMemberSalary::find($id);

        if ( !$item ) {
            return Response::error(__('Company Member Salary not found.'));
        }

        $item = EditPresenter::run($item);

        return Response::success(compact('item'));
    }
}