<?php
namespace App\Domains\Invoice\Presenters;

class EditPresenter
{
    public static function run($item) {
        unset($item->created_at);
        unset($item->updated_at);

        $item->lines = decode_json($item->lines);

        return $item;
    }
}
