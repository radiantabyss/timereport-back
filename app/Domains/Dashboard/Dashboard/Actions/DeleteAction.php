<?php
namespace App\Domains\Dashboard\Dashboard\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;

class DeleteAction extends Action
{
    public function run($id) {
        $item = Model\Dashboard::find($id);

        if ( !$item ) {
            return Response::error('Dashboard not found.');
        }

        $count = Model\Dashboard::count();
        if ( $count == 1 ) {
            return Response::error('At least 1 Dashboard is required.');
        }

        $item->delete();

        return Response::success();
    }
}
