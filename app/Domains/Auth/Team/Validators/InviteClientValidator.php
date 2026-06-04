<?php
namespace App\Domains\Auth\Team\Validators;

use App\Models as Model;

class InviteClientValidator
{
    public static function run($data) {
        //validate request params
        $validator = \Validator::make($data, [
            'email' => 'required|email',
            'client_id' => 'required',
        ], [
            'email.required' => 'Email is required',
            'email.email' => 'Email is invalid',
            'client_id.required' => 'Client ID is required',
        ]);

        if ( $validator->fails() ) {
            return $validator->messages();
        }

        //check if user already exists
        $exists = Model\User::where('email', trim($data['email']))->exists();
        if ( $exists ) {
            return 'A user with this email already exists.';
        }

        //check if client exists
        $exists = Model\Client::find($data['client_id']);
        if ( !$exists ) {
            return 'Client not found.';
        }

        //check if client already has an account
        $exists = Model\User::where('client_id', $data['client_id'])->exists();
        if ( $exists ) {
            return 'Client already has an account.';
        }

        return true;
    }
}
