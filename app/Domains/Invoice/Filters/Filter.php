<?php
namespace App\Domains\Invoice\Filters;

use RA\Filter as RA_Filter;

class Filter extends RA_Filter
{
    protected static $table = 'invoice';

    protected static function status($value) {
        //if all is selected then ignore reverse invoices
        if ( !$value ) {
            return self::$query->where('status', '!=', 'reverse');
        }

        self::$query->where('status', $value);
    }

    protected static function window($value) {
        if ( $value == 'none' ) {
            return;
        }

        self::$query->applyWindowConditions($value);
    }

    protected static function export($value) {}
    protected static function tab($value) {}
}
