<?php
namespace App\Domains\Supplier\Presenters;

class EditPresenter
{
    public static function run($item) {
        unset($item->created_at);
        unset($item->updated_at);

        return $item;
    }
}