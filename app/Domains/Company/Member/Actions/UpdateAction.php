<?php
namespace App\Domains\Company\Member\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Company\Member\Presenters\Presenter;
use App\Domains\Company\Member\Transformers\Transformer;
use App\Domains\Company\Member\Validators\Validator;

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
        $item = Model\CompanyMember::find($id);
        $item->update($data);
        $item = Presenter::run($item);

        return Response::success(compact('item'));
    }
}
