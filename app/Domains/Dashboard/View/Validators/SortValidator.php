<?php
namespace App\Domains\Dashboard\View\Validators;

use App\Models as Model;

class SortValidator
{
    public static function run($data) {
        //validate request params
        $validator = \Validator::make($data, [
            'id' => 'required',
            'id2' => 'required',
        ], [
            'id.required' => 'ID is required',
            'id2.required' => 'ID2 is required',
        ]);

        if ( $validator->fails() ) {
            return $validator->messages();
        }

        return true;
    }
}
