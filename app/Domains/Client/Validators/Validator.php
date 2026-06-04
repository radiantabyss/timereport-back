<?php
namespace App\Domains\Client\Validators;

use App\Models as Model;

class Validator
{
    public static function run($data, $id = false) {
        //check if item exists
        if ( $id ) {
            $item = Model\Client::find($id);

            if ( !$item ) {
                return to_words(\Domain::name()).' not found.';
            }
        }

        //validate request params
        $validator = \Validator::make($data, [
            'name' => 'required',
        ], [
            'name.required' => 'Name is required',
        ]);

        if ( $validator->fails() ) {
            return $validator->messages();
        }

        return true;
    }
}
