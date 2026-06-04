<?php
namespace App\Domains\Project\Presenters;

class EditPresenter
{
    public static function run($item) {
        unset($item->created_at);
        unset($item->updated_at);

        return $item;
    }
}
