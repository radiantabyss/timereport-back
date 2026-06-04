<?php
namespace App\Domains\Invoice\Presenters;

class ListPresenter
{
    public static function run($items) {
        foreach ( $items as $item ) {
            unset($item->lines);
        }
        
        return $items;
    }
}
