<?php
namespace App\Domains\InvoiceTemplate\Transformers;

class PatchTransformer
{
    public static function run($data) {
        $allowed_fields = ['is_active'];
        foreach ( $data as $key => $value ) {
            if ( !in_array($key, $allowed_fields) ) {
                unset($data[$key]);
            }
        }

        return $data;
    }
}
