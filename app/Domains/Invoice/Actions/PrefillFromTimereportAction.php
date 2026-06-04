<?php
namespace App\Domains\Invoice\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Invoice\Validators\PrefillFromTimereportValidator;

class PrefillFromTimereportAction extends Action
{
    public function run() {
        $data = \Request::all();

        //validate request
        $validation = PrefillFromTimereportValidator::run($data);
        if ( $validation !== true ) {
            return Response::error($validation);
        }

        if ( $data['project_id'] ) {
            $project = Model\Project::with('client.default_invoice_template', 'client.company')->find($data['project_id']);
            $client = $project->client;
        }
        else {
            $project = null;
            $client = Model\Client::with('default_invoice_template', 'company')->find($data['client_id']);
        }

        $company_tax = Model\CompanyTax::orderBy('start_date', 'desc')->first();

        $prefill = [
            'window' => $data['window'],
            'client_id' => $client->id,
            'project_id' => $data['project_id'] ?? '',
            'template_id' => $client->default_invoice_template_id,
            'currency' => $client->default_currency,
            'vat' => $client->company->vat_payer ? $company_tax->vat : '',
            'lines' => $this->lines($data, $client, $project),
        ];

        return Response::success(compact('prefill'));
    }

    private function lines($data, $client, $project) {
        $parsed_window = \Date::parseWindow($data['window']);
        $description = 'Web Development';
        if ( $parsed_window ) {
            $description .= ' '.(date('d M Y', strtotime($parsed_window['start'])).' - '.date('d M Y', strtotime($parsed_window['end'])));
        }

        $quantity = 1;

        $entries = Model\Timereport::select(\DB::raw('SUM(hours) as hours'), 'currency', 'rate')
            ->where('client_id', $client->id)
            ->where(function($query) use($project) {
                if ( $project ) {
                    $query->where('project_id', $project->id);
                }
            })
            ->applyWindowConditions($data['window'])
            ->groupBy('currency', 'rate')
            ->get();

        $lines = [];
        foreach ( $entries as $entry ) {
            $unit_price = $entry->hours * $entry->rate;

            //get conversion rate
            $conversion_rate = null;
            if ( $client->default_invoice_template && $entry->currency != $client->default_invoice_template->currency ) {
                $conversion_rate = Model\ConversionRate::where('source', \Auth::user()->team->meta['conversion_rate_source'])
                    ->where('from', $entry->currency)
                    ->where('to', $client->default_invoice_template->currency)
                    ->where('date', date('Y-m-d'))
                    ->first();
            }

            $lines[] = [
                'description' => $description ."\n".$entry->hours.'h @ '.$entry->rate.config('settings.currency_symbols')[$entry->currency].'/h',
                'quantity' => 1,
                'unit_price' => number_format(($conversion_rate->rate ?? 1) * $entry->hours * $entry->rate, 2, '.', ''),
            ];
        }

        return $lines;
    }
}
