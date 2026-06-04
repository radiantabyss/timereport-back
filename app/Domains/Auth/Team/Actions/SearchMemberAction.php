<?php
namespace App\Domains\Auth\Team\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use RA\Auth\Models\TeamMember;

class SearchMemberAction extends Action
{
    public function run() {
        $data = \Request::all();

        $query = TeamMember::select('user.name', 'team_member.user_id')
            ->leftJoin('user', 'user.id', '=', 'team_member.user_id')
            ->where(function($query) use($data) {
                if ( isset($data['id']) && is_numeric($data['id']) ) {
                    $query->where('user.id', $data['id']);
                }
                else {
                    $query->where('user.id', $data['term'])
                        ->orWhere('user.name', 'LIKE', '%'.$data['term'].'%')
                        ->orWhere('user.email', 'LIKE', '%'.$data['term'].'%');
                }
            })
            ->whereIn('user.type', ['user', 'admin', 'super_admin'])
            ->where('user.is_active', true)
            ->where('team_id', \Auth::user()->team->id)
            ->limit($data['limit']);

        $data['order_by'] = $data['order_by'] ?? 'user.name';
        $data['order'] = $data['order'] ?? 'asc';
        $query->orderBy($data['order_by'], $data['order']);

        $items = $query->get();

        $items = $items->map(function($item) {
            return [
                'text' => $item->name,
                'value' => $item->user_id,
            ];
        });

        return Response::success(compact('items'));
    }
}
