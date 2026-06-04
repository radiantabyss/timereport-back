<?php
namespace App\Support;

use Illuminate\Support\Facades\Gate;

class Gates
{
    public static function register() {
        Gate::define('add-timereport', function($user) {
            if ( $user->type == 'client' ) {
                return false;
            }

            return true;
        });

        Gate::define('delete-timereport', function($user, $item) {
            //check type
            if ( $user->type == 'client' ) {
                return false;
            }

            //check if it belongs to the user
            if ( $user->id != $item->user_id ) {
                return false;
            }

            //check if it's old
            if ( $item->created_at < date('Y-m-d H:i:s', strtotime('-2 days'))  ) {
                return false;
            }

            return true;
        });

        Gate::define('reverse-invoice', function($user, $item) {
            //check type
            if ( $user->type == 'client' ) {
                return false;
            }

            //only invoices uploaded to efactura can be reversed. otherwise they can just be deleted
            if ( !$item->efactura_status ) {
                return false;
            }

            return true;
        });

        Gate::define('delete-invoice', function($user, $item) {
            //check type
            if ( $user->type == 'client' ) {
                return false;
            }

            //invoices uploaded to efactura can't be deleted anymore
            if ( $item->efactura_status ) {
                return false;
            }

            //new invoices can be deleted
            if ( $item->status == 'new' ) {
                return true;
            }

            if ( $item->status == 'reverse' && $item->created_at > date('Y-m-d H:i:s', strtotime('-2 days'))  ) {
                return true;
            }

            return false;
        });
    }
}
