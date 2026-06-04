<?php
namespace App\Domains\Client\Presenters;

use App\Models as Model;
use App\Domains\Company\Company\Presenters\Presenter as CompanyPresenter;

class EditPresenter
{
    public static function run($item) {
        unset($item->created_at);
        unset($item->updated_at);

        if ( $item->company_id ) {
            $company = Model\Company::find($item->company_id);
            $item->company = CompanyPresenter::run($company);
        }

        return $item;
    }
}
