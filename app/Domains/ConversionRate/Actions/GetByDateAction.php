<?php
namespace App\Domains\ConversionRate\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\ConversionRate\Validators\GetByDateValidator;

class GetByDateAction extends Action
{
    public function run() {
        $data = \Request::all();

        //validate request
        $validation = GetByDateValidator::run($data);
        if ( $validation !== true ) {
            return Response::error($validation);
        }

        $item = Model\ConversionRate::where('source', $data['source'])
            ->where('date', $data['date'])
            ->where('from', $data['currency'])
            ->where('to', \Auth::user()->company->currency)
            ->first();

        if ( !$item ) {
            return Response::error('Conversion rate not found for the selected date and currency.');
        }

        return Response::success(compact('item'));
    }
}
