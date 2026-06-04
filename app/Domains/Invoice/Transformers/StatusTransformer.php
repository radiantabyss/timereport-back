<?php
namespace App\Domains\Invoice\Transformers;

class StatusTransformer
{
    public static function run($data) {
        $allowed_fields = ['status', 'total_received'];
        foreach ( $data as $key => $value ) {
            if ( !in_array($key, $allowed_fields) ) {
                unset($data[$key]);
            }
        }

        if ( $data['status'] != 'paid' ) {
            unset($data['total_received']);
        }

        return $data;
    }
}
