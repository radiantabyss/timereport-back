<?php
namespace App\Domains\ExpenseInvoice\Filters;

use RA\Filter as RA_Filter;

class Filter extends RA_Filter
{
    protected static $table = 'expense_invoice';

    protected static function window($value) {
        if ( $value == 'none' ) {
            return;
        }

        self::$query->applyWindowConditions($value);
    }

    protected static function name($value) {

    }

    protected static function r($value) {}
}
