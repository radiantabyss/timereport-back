<?php
namespace App\Domains\Timereport\Transformers;

use App\Models as Model;

class Transformer
{
    public static function run($data, $id = null) {
        $project = Model\Project::find($data['project_id']);
        $data['rate'] = $project->rate;
        $data['currency'] = $project->currency;

        $company_member = Model\CompanyMember::where('user_id', \Auth::user()->id)->first();
        if ( $company_member ) {
            $data['company_member_id'] = $company_member->id;
        }

        return $data;
    }
}
