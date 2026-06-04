<?php
namespace App\Domains\Invoice\Validators;

use App\Models as Model;

class StatusValidator
{
    public static function run($data, $id) {
        //check if item exists
        $item = Model\Invoice::find($id);
        if ( !$item ) {
            return __('Invoice not found.');
        }

        //validate request params
        $validator = \Validator::make($data, [
            'status' => 'required',
        ], [
            'status.required' => __('Status is required'),
        ]);

        if ( $validator->fails() ) {
            return $validator->messages();
        }

        return true;
    }
}
