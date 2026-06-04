<?php
namespace App\Domains\Dashboard\Panel\Presenters;

class Presenter
{
    public static function run($item) {
        $item->load('dashboard_view');
        return $item;
    }
}
