<?php
namespace App\Domains\Client\Transformers;

class Transformer
{
    public static function run($data, $id = null) {
        unset($data['company_id']);
        
        return $data;
    }
}
