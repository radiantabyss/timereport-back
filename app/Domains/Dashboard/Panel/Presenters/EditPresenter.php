<?php
namespace App\Domains\Dashboard\Panel\Presenters;

class EditPresenter
{
    public static function run($item) {
        $item->filters = decode_json($item->filters);
        $item->settings = decode_json($item->settings);
        $item->layout = decode_json($item->layout);

        return $item;
    }
}
