<?php
namespace App\Domains\Auth\User\Presenters;

use App\Models as Model;
use App\Domains\Company\Company\Presenters\Presenter as CompanyPresenter;

class Presenter
{
    public static function run($item, $team_id = null) {
        //load meta
        $item->loadMeta();

        //remove unwanted user fields
        unset($item->password);
        unset($item->created_at);
        unset($item->updated_at);

        //load current or first team and role
        $team = Model\Team::select('team.*', 'team_member.role')
            ->leftJoin('team_member', 'team_member.team_id', '=', 'team.id')
            ->where('team_member.user_id', $item->id)
            ->where(function($query) use($team_id) {
                if ( $team_id ) {
                    $query->where('team.id', $team_id);
                }
            })
            ->orderBy('id')
            ->first();

        if ( $team ) {
            $team->loadMeta();
        }

        $item->team = $team;

        //load team
        if ( isset($team->meta['company_id']) ) {
            $company = Model\Company::find($team->meta['company_id']);
            $item->company = CompanyPresenter::run($company);
        }
        else {
            $item->company = null;
        }

        return $item;
    }
}
