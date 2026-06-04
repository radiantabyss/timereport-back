<?php
namespace App\Domains\Dashboard\View\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Dashboard\View\Validators\SortValidator;

class SortAction extends Action
{
    public function run($dashboard_id) {
        $dashboard = Model\Dashboard::find($dashboard_id);

        if ( !$dashboard ) {
            return Response::error('Dashboard not found.');
        }

        $data = \Request::all();

        //validate request
        $validation = SortValidator::run($data);
        if ( $validation !== true ) {
            return Response::error($validation);
        }

        $item = Model\DashboardView::find($data['id']);
        $item2 = Model\DashboardView::find($data['id2']);

        $position = $item->position;
        $position2 = $item2->position;

        if ( $position > $position2 ) {
            Model\DashboardView::where('position', '>=', $position2)
                ->where('position', '<', $position)
                ->orderBy('position', 'asc')
                ->update([
                    'position' => \DB::raw('position + 1'),
                ]);
        }
        else {
            Model\DashboardView::where('position', '<=', $position2)
                ->where('position', '>', $position)
                ->orderBy('position', 'desc')
                ->update([
                    'position' => \DB::raw('position - 1'),
                ]);
        }

        $item->update([
            'position' => $position2,
        ]);

        return Response::success();
    }
}
