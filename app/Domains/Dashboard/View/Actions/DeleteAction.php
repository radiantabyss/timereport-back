<?php
namespace App\Domains\Dashboard\View\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;

class DeleteAction extends Action
{
    public function run($id) {
        $item = Model\DashboardView::find($id);

        if ( !$item ) {
            return Response::error(\Domain::name().' not found.');
        }

        $count = Model\DashboardView::where('dashboard_id', $item->dashboard_id)->count();
        if ( $count == 1 ) {
            return Response::error('At least 1 Dashboard View is required per Dashboard.');
        }

        $item->delete();
        Model\DashboardPanel::where('dashboard_view', $id)->delete();

        return Response::success();
    }
}
