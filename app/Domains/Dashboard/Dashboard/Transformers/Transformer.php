<?php
namespace App\Domains\Dashboard\Dashboard\Transformers;

use App\Models as Model;

class Transformer
{
    public static function run($data) {
        $data['position'] = Model\Dashboard::max('position') + 1;
        return $data;
    }
}
