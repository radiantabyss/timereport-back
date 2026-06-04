<?php
namespace App\Domains\Company\Company\Validators;

use App\Models as Model;

class Validator
{
    public static function run($data) {
        if ( isset($data['client_id']) ) {
            $client = Model\Client::find($data['client_id']);
            if ( !$client ) {
                return 'Client not found.';
            }
        }

        return true;
    }
}
