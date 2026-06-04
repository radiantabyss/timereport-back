<?php
namespace App\Domains\Location\City\Validators;

use App\Models as Model;

class Validator
{
    public static function run($data, $id = null) {
        //check if item exists
        if ( $id ) {
            $item = Model\City::find($id);
            if ( !$item ) {
                return __('Location City not found.');
            }
        }

        //validate request params
        $validator = \Validator::make($data, [
            'name' => 'required',
        ], [
            'name' => __('Name is required'),
        ]);

        if ( $validator->fails() ) {
            return $validator->messages();
        }

        return true;
    }
}