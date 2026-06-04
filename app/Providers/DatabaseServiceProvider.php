<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class DatabaseServiceProvider extends ServiceProvider
{
    public function boot() {
        \Schema::defaultStringLength(191);

        $folders = \File::directories(database_path('migrations'));
        foreach ( $folders as $folder ) {
            $this->loadMigrationsFrom($folder);
        }
    }
}
