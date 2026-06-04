<?php
namespace App\Domains\Client\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;

class SearchAction extends Action
{
    public function run() {
        $data = \Request::all();

        $query = Model\Client::where(function($query) use($data) {
                if ( isset($data['id']) && is_numeric($data['id']) ) {
                    $query->where('id', $data['id']);
                }
                else {
                    $query->where('id', $data['term'])
                        ->orWhere('name', 'LIKE', '%'.$data['term'].'%');
                }
            })
            ->limit($data['limit']);

        if ( isset($data['is_active']) && $data['is_active'] == 1 ) {
            $query->where('is_active', true);
        }

        $data['order_by'] = $data['order_by'] ?? 'id';
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
