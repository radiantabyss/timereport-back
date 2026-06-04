<?php
namespace App\Domains\Auth\Team\Transformers;

use App\Models as Model;

class InviteClientTransformer
{
    public static function run($data) {
        $password = \Str::random(8);

        $data['uuid'] = \Str::uuid();
        $data['type'] = 'client';
        $data['password'] = \Hash::make($password);
        $data['raw_password'] = $password;

        $client = Model\Client::find($data['client_id']);
        $data['name'] = $client->name;

        return $data;
    }
}
