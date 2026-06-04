<?php
namespace App\Domains\ExpenseInvoice\Presenters;

class ListPresenter
{
    public static function run($items) {
        foreach ( $items as $item ) {
            $item->name = pathinfo($item->path)['basename'];
        }

        return $items;
    }
}
