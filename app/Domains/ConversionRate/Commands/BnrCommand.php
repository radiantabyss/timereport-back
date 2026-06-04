<?php
namespace App\Domains\ConversionRate\Commands;

use Illuminate\Console\Command;
use App\Models as Model;

class BnrCommand extends Command
{
    protected $signature = 'conversion-rate:bnr {year?}';
    protected $description = 'Fetches conversion rates from BNR';

    public function handle() {
        $year = $this->argument('year') ?? date('Y');

        $xml = simplexml_load_string(file_get_contents('https://www.bnr.ro/files/xml/years/nbrfxrates'.$year.'.xml'));
        $entries = $xml->Body->Cube;
        $date = ($year - 1).'-12-31';

        $entries_by_date = [];
        foreach ( $entries as $entry ) {
            $date = (string) $entry->attributes()['date'];
            $entries_by_date[$date] = [];

            foreach ( $entry->Rate as $rate ) {
                $currency = (string) $rate->attributes()['currency'];

                if ( !in_array($currency, config('settings.currencies')) ) {
                    continue;
                }

                $entries_by_date[$date][$currency] = (float) $rate;
            }
        }

        $start = ($year - 1).'-12-28';
        $end = $year.'-12-31';

        $last_dates = \Date::range($start, ($year - 1).'-12-31');
        rsort($last_dates);

        foreach ( $last_dates as $date ) {
            $conversion_rates = Model\ConversionRate::where('date', $date)
                ->where('source', 'bnr')
                ->get();

            foreach ( $conversion_rates as $conversion_rate ) {
                $entries_by_date[$date][$conversion_rate->from] = (float) $conversion_rate->rate;
            }
        }

        krsort($entries_by_date);

        $dates = \Date::range($start, $end);
        rsort($dates);

        foreach ( $dates as $date ) {
            if ( $date > date('Y-m-d') ) {
                continue;
            }

            if ( !isset($entries_by_date[$date]) ) {
                foreach ( $entries_by_date as $entry_date => $entries ) {
                    if ( $date > $entry_date ) {
                        break 1;
                    }
                }
            }
            else {
                $entries = $entries_by_date[$date];
            }

            foreach ( $entries as $currency => $rate ) {
                Model\ConversionRate::updateOrCreate([
                    'source' => 'bnr',
                    'from' => $currency,
                    'to' => 'RON',
                    'date' => $date,
                ], [
                    'source' => 'bnr',
                    'from' => $currency,
                    'to' => 'RON',
                    'date' => $date,
                    'rate' => (float) $rate,
                ]);
            }
        }
    }
}
