<?php
namespace App\Domains\ConversionRate\Validators;

use App\Models as Model;

class GetByDateValidator
{
    public static function run($data) {
        //validate request params
        $validator = \Validator::make($data, [
            'currency' => 'required',
            'date' => 'required',
            'source' => 'required',
        ], [
            'currency.required' => 'Currency is required',
            'date.required' => 'Date is required',
            'source.required' => 'Source is required',
        ]);

        if ( $validator->fails() ) {
            return $validator->messages();
        }

        return true;
    }
}
