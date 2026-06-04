<?php
namespace App\Domains\Invoice\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;

class SearchAction extends Action
{
    public function run() {
        $data = \Request::all();

        $query = Model\Invoice::with('client')
            ->where(function($query) use($data) {
                if ( isset($data['id']) && is_numeric($data['id']) ) {
                    $query->where('id', $data['id']);
                }
                else if ( isset($data['term']) ) {
                    $query->where('number', 'LIKE', $data['term'].'%')
                        ->orWhere('number', 'LIKE', '%'.$data['term'].'%')
                        ->orWhereRelation('client', 'name', 'LIKE', '%'.$data['term'].'%')
                        ->orWhereRaw('CONCAT_WS(\'\', series, number) LIKE \'%'.$data['term'].'%\'');
                }
            })
            ->limit($data['limit'])
            ->orderBy('number', 'desc');

        if ( isset($data['status']) && $data['status'] ) {
            $query->where('status', $data['status']);
        }

        $items = $query->get();

        $items = $items->map(function($item) {
            return [
                'text' => $item->series.$item->number.' / '.$item->client->name,
                'value' => $item->id,
            ];
        });

        return Response::success(compact('items'));
    }
}
