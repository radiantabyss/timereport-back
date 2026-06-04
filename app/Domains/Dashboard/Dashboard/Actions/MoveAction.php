<?php
namespace App\Domains\Dashboard\Dashboard\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;

class MoveAction extends Action
{
    public function run($id) {
        $data = \Request::all();
        $item = Model\Dashboard::find($id);

        if ( !$item ) {
            return Response::error('Dashboard not found.');
        }

        $position = $item->position;

        if ( $data['direction'] == 'up' ) {
            $operator = '<';
            $order = 'desc';
        }
        else {
            $operator = '>';
            $order = 'asc';
        }

        $item2 = Model\Dashboard::where('position', $operator, $position)
            ->orderBy('position', $order)
            ->first();

        if ( !$item2 ) {
            return Response::success();
        }

        $item->update([
            'position' => $item2->position,
        ]);

        $item2->update([
            'position' => $position,
        ]);

        return Response::success();
    }
}
