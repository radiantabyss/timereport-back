<?php
namespace App\Domains\Location\City\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;

class SearchAction extends Action
{
    public function run() {
        $data = \Request::all();

        $query = Model\City::where(function($query) use($data) {
                if ( isset($data['id']) && is_numeric($data['id']) ) {
                    $query->where('id', $data['id']);
                }
                else {
                    $query->where('id', $data['term'])
                        ->orWhere('name', 'LIKE', '%'.$data['term'].'%');
                }
            })
            ->limit($data['limit']);

        if ( isset($data['country_id']) && $data['country_id'] ) {
            $query->where('country_id', $data['country_id']);
        }

        if ( isset($data['state_id']) && $data['state_id'] ) {
            $query->where('state_id', $data['state_id']);
        }

        $data['order_by'] = $data['order_by'] ?? 'name';
        $data['order'] = $data['order'] ?? 'asc';
        $query->orderBy($data['order_by'], $data['order']);

        $items = $query->get();

        $items = $items->map(function($item) {
            return [
                'text' => $item->name,
                'value' => $item->id,
            ];
        });

        return Response::success(compact('items'));
    }
}
