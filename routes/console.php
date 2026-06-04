<?php
use App\Models as Model;

Artisan::command('test', function() {
    // \Cache::set('asd', 'asd1');
    dmp(\Cache::get('asd'));
});
