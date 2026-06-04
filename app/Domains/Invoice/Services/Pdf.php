<?php
namespace App\Domains\Invoice\Services;

use App\Models as Model;

class Pdf
{
    public static function run($item) {
        \App::setLocale($item->template->lang);

        $content = self::parseShortcodes($item);
        $pdf = \PDF::loadView('AppDomains::Invoice.views.pdf', compact('content'));
        $pdf->getDomPDF()->setPaper('A4', 'portrait');

        return $pdf;
    }

    private static function parseShortcodes($item) {
        $content = $item->template->content;

        foreach ( $item->toArray() as $key => $value ) {
            if ( in_array($key, ['client', 'contract', 'company', 'client_company', 'template']) && $item->$key ) {
                foreach ( $item->$key->toArray() as $sub_key => $sub_value ) {
                    $content = str_replace('{'.$key.'.'.$sub_key.'}', $item->$key->$sub_key, $content);
                }
            }
            else if ( in_array($key, ['date', 'due_date', 'conversion_rate_date']) ) {
                $content = str_replace('{invoice.'.$key.'}', date('d.m.Y', strtotime($item->$key)), $content);
            }
            else {
                $content = str_replace('{invoice.'.$key.'}', $item->$key, $content);
            }
        }

        $content = str_replace('{invoice.date_us_format}', date('m/d/Y', strtotime($item->date)), $content);
        $content = str_replace('{invoice.due_date_us_format}', date('m/d/Y', strtotime($item->due_date)), $content);
        $content = str_replace('{invoice.conversion_rate_date_us_format}', date('m/d/Y', strtotime($item->due_date)), $content);
        $content = self::parseLinesShortcode($item, $content);
        $content = self::parseTimereportShorcode($item, $content);

        return $content;
    }

    private static function parseLinesShortcode($item, $content) {
        $lines_content = '<table cellpadding="5"><thead>
        <tr>
            <td>'.__('No.').'</td>
            <td>'.__('Description').'</td>
            <td>'.__('Unit').'</td>
            <td>'.__('QTY.').'</td>
            <td>'.__('Unit Price (excl. VAT)').' - '.$item->currency.' -</td>
            <td>'.__('Value (excl. VAT)').' - '.$item->currency.' -</td>
            <td>'.__('VAT %').'</td>
            <td>'.__('VAT amount').' - '.$item->currency.' -</td>
            <td>'.__('Total amount').' - '.$item->currency.' -</td>
        </tr>
        </thead><tbody>';
        $lines = decode_json($item->lines);

        foreach ( $lines as $i => $line ) {
            $line['unit_price'] = str_replace(' ', '', $line['unit_price']);
            $line_price_with_vat = $line['unit_price'] + $line['unit_price'] * $item->vat / 100;

            $lines_content .= '<tr>
                <td>'.($i + 1).'</td>
                <td style="font-size: 16px; line-height: 14px;">'.nl2br($line['description']).'</td>
                <td>-</td>
                <td>'.$line['quantity'].'</td>
                <td>'.$line['unit_price'].'</td>
                <td>'.$line['unit_price'].'</td>
                <td>'.$item->vat.'</td>
                <td>'.number_format($line_price_with_vat - $line['unit_price'], 2, '.', '').'</td>
                <td>'.number_format($line_price_with_vat, 2, '.', '').'</td>
            </tr>';
        }

        $lines_content .= '
            <tr>
                <td colspan="5"><center>TOTAL (including VAT) - '.$item->currency.' -</center></td>
                <td colspan="4">'.$item->total_with_vat.'</td>
            </tr>
        </tbody></table>';

        $content = str_replace('{lines}', $lines_content, $content);

        return $content;
    }

    private static function parseTimereportShorcode($item, $content) {
        $timereports = Model\Timereport::select([
                'timereport.*', 'company_member.name', 'company_member.short_name',
                'project.name as project_name',
            ])
            ->leftJoin('company_member', 'company_member.id', '=', 'timereport.company_member_id')
            ->leftJoin('project', 'project.id', '=', 'timereport.project_id')
            ->where('invoice_id', $item->id)
            ->orderBy('timereport.date', 'asc')
            ->get();

        if ( !count($timereports) ) {
            $content = str_replace('{timereport}', '', $content);
            return $content;
        }

        $timereport_content = '
        <div class="page-break"></div>
        <div class="title" style="margin-top: 20px;">'.__('Timereport').'</div>
        <table cellpadding="5" style="font-size: 14px;">
            <thead>
                <tr>
                    <th>'.__('User').'</th>
                    <th>'.__('Project').'</th>
                    <th>'.__('Date').'</th>
                    <th>'.__('Hours').'</th>
                    <th>'.__('Amount').'</th>
                    <th>'.__('Description').'</th>
                </tr>
            </thead>
            <tbody>';

        foreach ( $timereports as $timereport ) {
            $timereport_content .= '<tr>
                <td>'.($timereport->short_name ?? $timereport->name).'</td>
                <td>'.$timereport->project_name.'</td>
                <td style="white-space: nowrap;">'.
                    date('d', strtotime($timereport->date)).' '.__(date('M', strtotime($timereport->date))).' '.date('Y', strtotime($timereport->date))
                .'</td>
                <td>'.$timereport->hours.'</td>
                <td>'.$timereport->rate * $timereport->hours.' '.config('settings.currency_symbols')[$timereport->currency].'</td>
                <td>'.$timereport->description.'</td>
            </tr>';
        }

        $timereport_content .= '</tbody></table>';

        $content = str_replace('{timereport}', $timereport_content, $content);

        return $content;
    }
}
