<?php
namespace App\Domains\Project\Validators;

use App\Models as Model;

class Validator
{
    public static function run($data, $id = false) {
        //check if item exists
        if ( $id ) {
            $item = Model\Project::find($id);

            if ( !$item ) {
                return to_words(\Domain::name()).' not found.';
            }
        }

        //validate request params
        $validator = \Validator::make($data, [
            'client_id' => 'required',
            'name' => 'required',
            'rate' => 'required',
            'currency' => 'required',
        ], [
            'client_id.required' => 'Client ID is required',
            'name.required' => 'Name is required',
            'rate.required' => 'Rate is required',
            'currency.required' => 'Currency is required',
        ]);

        if ( $validator->fails() ) {
            return $validator->messages();
        }

        //validate client
        $client = Model\Client::find($data['client_id']);
        if ( !$client ) {
            return 'Client not found.';
        }

        return true;
    }
}
