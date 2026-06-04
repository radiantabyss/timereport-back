<?php
namespace App\Domains\Auth\Team\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;

class DeleteClientAction extends Action
{
    public function run($id) {
        $item = Model\User::leftJoin('client', 'client.id', '=', 'user.client_id')
            ->where('user.id', $id)
            ->where('client.team_id', \Auth::user()->team->id)
            ->first();

        if ( !$item ) {
            return Response::error(__('Client Account not found.'));
        }

        $item->delete();

        //delete team
        $team = Model\Team::where('user_id', $id)->first();
        Model\Team::where('user_id', $id)->delete();

        //delete team members
        $team_members = Model\TeamMember::where('team_id', $team->id)->get();
        Model\TeamMember::where('team_id', $team->id)->delete();
        Model\User::whereIn('id', pluck($team_members, 'user_id'))->delete();

        return Response::success();
    }
}
