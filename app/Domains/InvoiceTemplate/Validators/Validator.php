<?php
namespace App\Domains\InvoiceTemplate\Validators;

use App\Models as Model;

class Validator
{
    public static function run($data, $id = false) {
        //check if item exists
        if ( $id ) {
            $item = Model\InvoiceTemplate::find($id);

            if ( !$item ) {
                return to_words(\Domain::name()).' not found.';
            }
        }

        //validate request params
        $validator = \Validator::make($data, [
            'name' => 'required',
            'currency' => 'required',
        ], [
            'name.required' => 'Name is required',
            'currency.required' => 'Currency is required',
        ]);

        if ( $validator->fails() ) {
            return $validator->messages();
        }

        return true;
    }
}
