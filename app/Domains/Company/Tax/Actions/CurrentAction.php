<?php
namespace App\Domains\Company\Tax\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;

class CurrentAction extends Action
{
    public function run() {
        $item = Model\CompanyTax::orderBy('start_date', 'desc')->first();
        return Response::success(compact('item'));
    }
}
