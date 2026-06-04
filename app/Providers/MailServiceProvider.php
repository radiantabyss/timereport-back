<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MailServiceProvider extends ServiceProvider
{
    public function boot() {
        //register mail views
        $domains = [
            'Common', 'Auth.Team'
        ];
        foreach ( $domains as $domain ) {
            \View::addNamespace($domain, __DIR__ . '/../Domains/'.str_replace('.', '/', $domain).'/Mail/views');
        }
    }
}
