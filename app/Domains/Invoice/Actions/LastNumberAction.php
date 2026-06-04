<?php
namespace App\Domains\Invoice\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;

class LastNumberAction extends Action
{
    public function run() {
        $number = Model\Invoice::max('number');
        $number++;

        $length = strlen((string) $number);
        for ( $i = 0; $i < 4 - $length; $i++ ) {
            $number = '0'.$number;
        }

        return Response::success(compact('number'));
    }
}
