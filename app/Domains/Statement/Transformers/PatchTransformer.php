<?php
namespace App\Domains\Statement\Transformers;

class PatchTransformer
{
    public static function run($data) {
        $allowed_fields = ['type', 'company_member_id', 'is_ignored', 'invoice_id'];

        foreach ( $data as $key => $value ) {
            if ( !in_array($key, $allowed_fields) ) {
                unset($data[$key]);
            }
        }

        return $data;
    }
}
