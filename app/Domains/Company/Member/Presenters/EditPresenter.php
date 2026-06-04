<?php
namespace App\Domains\Company\Member\Presenters;

class EditPresenter
{
    public static function run($item) {
        unset($item->id);
        unset($item->team_id);
        unset($item->created_by);
        unset($item->created_at);
        unset($item->updated_at);

        return $item;
    }
}
