<?php
namespace App\Domains\Company\Member\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;

class SearchAction extends Action
{
    public function run() {
        $data = \Request::all();

        $query = Model\CompanyMember::where(function($query) use($data) {
                if ( isset($data['id']) && is_numeric($data['id']) ) {
                    $query->where('id', $data['id']);
                }
                else {
                    $query->where('id', $data['term'])
                        ->orWhere('name', 'LIKE', '%'.$data['term'].'%')
                        ->orWhere('short_name', 'LIKE', '%'.$data['term'].'%');
                }
            })
            ->limit($data['limit']);

        $data['order_by'] = $data['order_by'] ?? 'id';
        $data['order'] = $data['order'] ?? 'asc';
        $query->orderBy($data['order_by'], $data['order']);

        $items = $query->get();

        $items = $items->map(function($item) {
            return [
                'text' => $item->short_name ?? $item->name,
                'value' => $item->id,
            ];
        });

        return Response::success(compact('items'));
    }
}
