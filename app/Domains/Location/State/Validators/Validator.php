<?php
namespace App\Domains\Location\State\Validators;

use App\Models as Model;

class Validator
{
    public static function run($data, $id = null) {
        //check if item exists
        if ( $id ) {
            $item = Model\State::find($id);
            if ( !$item ) {
                return __('Location State not found.');
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