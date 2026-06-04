<?php
namespace App\Domains\Company\MemberAssociate\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Company\MemberAssociate\Presenters\Presenter;
use App\Domains\Company\MemberAssociate\Transformers\Transformer;
use App\Domains\Company\MemberAssociate\Validators\Validator;

class CreateAction extends Action
{
    public function run() {
        $data = \Request::all();

        $validation = Validator::run($data);
        if ( $validation !== true ) {
            return Response::error($validation);
        }

        $data = Transformer::run($data);
        $item = Model\CompanyMemberAssociate::create($data);
        $item = Presenter::run($item);

        return Response::success(compact('item'));
    }
}