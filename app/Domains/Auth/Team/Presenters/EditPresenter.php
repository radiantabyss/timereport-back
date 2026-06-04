<?php
namespace App\Domains\Auth\Team\Presenters;

use Illuminate\Contracts\Encryption\DecryptException;

class EditPresenter
{
    public static function run($item) {
        $item->loadMeta();
        $meta = $item->meta;

        foreach ( config('ra-auth.encrypted_team_meta_fields') as $field ) {
            if ( !isset($meta[$field]) ) {
                continue;
            }

            try {
                $meta[$field] = \Crypt::decrypt($meta[$field]);
            }
            catch (DecryptException $e) {}
        }

        $item->meta = $meta;

        return $item;
    }
}
