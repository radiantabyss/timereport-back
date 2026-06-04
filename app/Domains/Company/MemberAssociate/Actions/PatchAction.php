<?php
namespace App\Domains\Company\MemberAssociate\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Company\MemberAssociate\Presenters\Presenter;
use App\Domains\Company\MemberAssociate\Transformers\PatchTransformer;
use App\Domains\Company\MemberAssociate\Validators\PatchValidator;

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
        $item = Model\CompanyMemberAssociate::find($id);
        $item->update($data);
        $item = Presenter::run($item);

        return Response::success(compact('item'));
    }
}
