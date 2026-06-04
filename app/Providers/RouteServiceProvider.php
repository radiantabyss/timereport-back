<?php
namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        // load auth routes
        \Route::middleware('routes')
            ->namespace('RA\Auth\Domains')
            ->group(base_path('routes/ra-auth.php'));

        //load routes
        \Route::middleware('routes')
            ->namespace('App\Domains')
            ->group(base_path('routes/routes.php'));

        //load routes that required auth
        if ( !\Auth::check() ) {
            return;
        }

        $user = \Auth::user();

        //load super admin routes
        if ( $user->type == 'super_admin' ) {
            \Route::middleware('routes')
                ->namespace('App\Domains')
                ->group(function () {
                    require base_path('routes/team-member.php');
                    require base_path('routes/team-admin.php');
                    require base_path('routes/team-owner.php');
                    require base_path('routes/super_admin.php');
                });
        }
        //load routes by team role
        else if ( $user->type == 'user' ) {
            if ( $user->team->role == 'owner' ) {
                \Route::middleware('routes')
                    ->namespace('App\Domains')
                    ->group(function () {
                        require base_path('routes/team-member.php');
                        require base_path('routes/team-admin.php');
                        require base_path('routes/team-owner.php');
                    });
            }
            else {
                \Route::middleware('routes')
                    ->namespace('App\Domains')
                    ->group(base_path('routes/team-'.$user->team->role.'.php'));
            }
        }
        //load client routes
        else if ( $user->type == 'client' ) {
            \Route::middleware('routes')
                ->namespace('App\Domains')
                ->group(base_path('routes/client.php'));
        }
    }
}
