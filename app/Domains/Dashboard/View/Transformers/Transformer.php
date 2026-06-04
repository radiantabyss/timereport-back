<?php
namespace App\Domains\Dashboard\View\Transformers;

use App\Models as Model;

class Transformer
{
    public static function run($data, $id = false) {
        if ( !$id ) {
            $data['position'] = Model\DashboardView::max('position') + 1;
        }
        
        return $data;
    }
}
