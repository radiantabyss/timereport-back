<?php
namespace App\Domains\Company\Tax\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Company\Tax\Presenters\Presenter;
use App\Domains\Company\Tax\Transformers\Transformer;
use App\Domains\Company\Tax\Validators\Validator;

class CreateAction extends Action
{
    public function run() {
        $data = \Request::all();

        $validation = Validator::run($data);
        if ( $validation !== true ) {
            return Response::error($validation);
        }

        //add end date to previous
        $previous = Model\CompanyTax::whereNull('end_date')
            ->orWhere('end_date', '>=', $data['start_date']);

        if ( $previous ) {
            $previous->update([
                'end_date' => date('Y-m-d', strtotime($data['start_date'].' -1 day')),
            ]);
        }

        $data = Transformer::run($data);
        $item = Model\CompanyTax::create($data);
        $item = Presenter::run($item);


        return Response::success(compact('item'));
    }
}
