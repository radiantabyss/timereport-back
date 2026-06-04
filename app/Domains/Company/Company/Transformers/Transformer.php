<?php
namespace App\Domains\Company\Company\Transformers;

class Transformer
{
    public static function run($data, $id = null) {
        if ( !isset($data['client_id']) ) {
            $data['client_id'] = null;
        }

        return $data;
    }
}
