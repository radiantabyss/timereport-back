<?php
namespace App\Domains\Statement\Filters;

use RA\Filter as RA_Filter;

class Filter extends RA_Filter
{
    protected static $table = 'statement';

    protected static function source($value, $operator) {
        if ( $operator == '!=' ) {
            self::$query->where('source', 'NOT LIKE', '%'.$value.'%');
        }
        else {
            self::$query->where('source', 'LIKE', '%'.$value.'%');
        }
    }

    protected static function description($value) {
        self::$query->where('description', 'LIKE', '%'.$value.'%');
    }

    protected static function window($value) {
        if ( $value == 'none' ) {
            return;
        }

        self::$query->applyWindowConditions($value);
    }

    protected static function export($value) {}
    protected static function r($value) {}
}
