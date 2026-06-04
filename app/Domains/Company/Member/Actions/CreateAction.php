<?php
namespace App\Domains\Company\Member\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Company\Member\Presenters\Presenter;
use App\Domains\Company\Member\Transformers\Transformer;
use App\Domains\Company\Member\Validators\Validator;

class CreateAction extends Action
{
    public function run() {
        $data = \Request::all();

        $validation = Validator::run($data);
        if ( $validation !== true ) {
            return Response::error($validation);
        }

        $data = Transformer::run($data);
        $item = Model\CompanyMember::create($data);
        $item = Presenter::run($item);

        return Response::success(compact('item'));
    }
}