<?php
namespace App\Domains\InvoiceTemplate\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;

class SearchAction extends Action
{
    public function run() {
        $data = \Request::all();

        $items = Model\InvoiceTemplate::where(function($query) use($data) {
                if ( isset($data['id']) && is_numeric($data['id']) ) {
                    $query->where('id', $data['id']);
                }
                else {
                    $query->where('id', $data['term'])
                        ->orWhere('name', 'LIKE', '%'.$data['term'].'%');
                }
            })
            ->limit($data['limit'])
            ->get();

        $items = $items->map(function($item) {
            return [
                'text' => $item->name,
                'value' => $item->id,
            ];
        });

        return Response::success(compact('items'));
    }
}
