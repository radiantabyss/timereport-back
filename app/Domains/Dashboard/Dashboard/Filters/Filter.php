<?php
namespace App\Domains\Dashboard\Dashboard\Filters;

use RA\Filter as LumiFilter;
use App\Models as Model;

class Filter extends LumiFilter
{
    protected static $table = 'dashboard';

    protected static function tab($value) {}
}
