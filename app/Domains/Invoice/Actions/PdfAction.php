<?php
namespace App\Domains\Invoice\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Invoice\Services\Pdf;

class PdfAction extends Action
{
    public function run($id) {
        $item = Model\Invoice::with([
                'client', 'contract',
                'company' => ['country', 'state', 'city'],
                'client_company' => ['country', 'state', 'city'],
                'template'
            ])
            ->find($id);

        $locations = ['country', 'state', 'city'];
        foreach ( $locations as $location ) {
            if ( $item->company->$location ) {
                $item->company->$location = $item->company->$location->name;
            }
            if ( $item->client_company->$location ) {
                $item->client_company->$location = $item->client_company->$location->name;
            }
        }

        if ( !$item ) {
            return Response::error('Not found.');
        }

        $pdf = Pdf::run($item);
        return $pdf->stream($item->series.$item->number.'.pdf');
    }
}
