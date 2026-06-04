<?php
namespace App\Domains\Company\MemberSalary\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Company\MemberSalary\Presenters\Presenter;
use App\Domains\Company\MemberSalary\Transformers\Transformer;
use App\Domains\Company\MemberSalary\Validators\Validator;

class CreateAction extends Action
{
    public function run() {
        $data = \Request::all();

        $validation = Validator::run($data);
        if ( $validation !== true ) {
            return Response::error($validation);
        }

        //update last current salary's end date
        Model\CompanyMemberSalary::where('company_member_id', $data['company_member_id'])
            ->whereNull('end_date')
            ->orderBy('id', 'desc')
            ->limit(1)
            ->update([
                'end_date' => date('Y-m-d', strtotime($data['start_date'].' -1 day')),
            ]);

        $data = Transformer::run($data);
        $item = Model\CompanyMemberSalary::create($data);
        $item = Presenter::run($item);

        return Response::success(compact('item'));
    }
}
