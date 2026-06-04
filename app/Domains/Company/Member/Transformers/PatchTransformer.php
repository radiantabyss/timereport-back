<?php
namespace App\Domains\Company\Member\Transformers;

class PatchTransformer
{
    private static $allowed_fields = [];

    public static function run($data) {
        foreach ( $data as $key => $value ) {
            if ( !in_array($key, self::$allowed_fields) ) {
                unset($data[$key]);
            }
        }

        return $data;
    }
}