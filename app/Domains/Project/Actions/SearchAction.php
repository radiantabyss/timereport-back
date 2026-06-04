<?php
namespace App\Domains\Project\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Project\Filters\Filter;

class SearchAction extends Action
{
    public function run() {
        $data = \Request::all();

        $query = Model\Project::withoutGlobalScope('team_exclusivity')
			->where(function($query) use($data) {
                if ( isset($data['id']) && is_numeric($data['id']) ) {
                    $query->where('id', $data['id']);
                }
                else {
                    $query->where('id', $data['term'])
                        ->orWhere('name', 'LIKE', '%'.$data['term'].'%');
                }
            })
			->where('is_active', true)
            ->limit($data['limit'])
            ->orderBy('name', 'asc');

		if ( \Auth::user()->type == 'client' ) {
			$query->where('client_id', \Auth::user()->client_id);
		}
		else {
			$query->where('team_id', \Auth::user()->team->id);
		}

        unset($data['term']);
        Filter::apply($query, $data);

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
