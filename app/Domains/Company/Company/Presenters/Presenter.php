<?php
namespace App\Domains\Company\Company\Presenters;

class Presenter
{
    public static function run($item) {
        unset($item->id);
        unset($item->team_id);
        unset($item->created_at);
        unset($item->updated_at);

        return $item;
    }
}
