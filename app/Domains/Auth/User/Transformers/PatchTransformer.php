<?php
namespace App\Domains\Auth\User\Transformers;

use App\Model as Model;

class PatchTransformer
{
    private static $allowed_fields = [
        'email', 'current_password', 'password', 'name',
    ];

    private static $allowed_meta_fields = [
        'profile_image_path', 'dark_mode', 'lang',
    ];

    public static function run($data) {
        $data = self::filterFields($data);
        $data = self::filterMetaFields($data);

        //update email
        if ( isset($data['email']) && $data['email'] != \Auth::user()->email ) {
            $exists = Model\User::where('email', $data['email'])->exists();
            if ( $exists ) {
                return 'An user with this email already exists.';
            }
        }

        //update password
        if ( isset($data['password']) ) {
            $data['password'] = \Hash::make($data['password']);
            unset($data['current_password']);
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
