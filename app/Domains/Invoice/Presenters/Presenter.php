<?php
namespace App\Domains\Invoice\Presenters;

class Presenter
{
    public static function run($item) {
        $item->lines = decode_json($item->lines);
        return $item;
    }
}
