<?php
namespace App\Domains\ExpenseInvoice\Transformers;

class PatchTransformer
{
    public static function run($data) {
        $allowed_fields = ['date'];

        foreach ( $data as $key => $value ) {
            if ( !in_array($key, $allowed_fields) ) {
                unset($data[$key]);
            }
        }

        return $data;
    }
}
