<?php
namespace App\Domains\Auth\Team\Transformers;

class PatchTransformer
{
    private static $allowed_fields = [
        'name',
    ];

    private static $allowed_meta_fields = [
        'image_path',
        'efactura_client_id', 'efactura_client_secret',
    ];

    public static function run($data) {
        $data = self::filterFields($data);
        $data = self::filterMetaFields($data);

        if ( isset($data['meta']) ) {
            foreach ( config('ra-auth.encrypted_team_meta_fields') as $field ) {
                if ( isset($data['meta'][$field]) ) {
                    $data['meta'][$field] = \Crypt::encrypt($data['meta'][$field]);
                }
            }
        }

        return $data;
    }

    private static function filterFields($data) {
        foreach ( $data as $key => $value ) {
            if ( $key != 'meta' && !in_array($key, self::$allowed_fields) ) {
                unset($data[$key]);
            }
        }

        return $data;
    }

    private static function filterMetaFields($data) {
        if ( !isset($data['meta']) ) {
            return $data;
        }

        $meta = $data['meta'];

        foreach ( $meta as $key => $value ) {
            if ( !in_array($key, self::$allowed_meta_fields) ) {
                unset($meta[$key]);
            }
        }

        $data['meta'] = $meta;

        return $data;
    }
}
