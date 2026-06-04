<?php
namespace App\Domains\Accounting\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use ZipStream\ZipStream;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Domains\Invoice\Services\Pdf;

class DownloadDocumentsAction extends Action
{
    private $window;
    private $files = [];
    private $base_path;

    public function run() {
        $this->window = \Request::get('window');
        $this->base_path = storage_path().'/accounting-documents/'.\Auth::user()->id.\Str::random(6);

        mkdir($this->base_path, 0777, true);
        mkdir($this->base_path.'/'.__('expenses'), 0777);

        //delete folder after execution
        register_shutdown_function(function () {
            \File::deleteDirectory($this->base_path);
        });

        $this->statement();
        $this->expenseInvoices();
        $this->invoices();

        $filename = __('documents');

        $response = new StreamedResponse(function() {
            $zip = new ZipStream(sendHttpHeaders: false);

            foreach ( $this->files as $file ) {
                $pathinfo = pathinfo($file['path']);
                $zip->addFileFromPath($file['subfolder'].'/'.$pathinfo['basename'], $file['path']);
            }

            $zip->finish();
        });

        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'.zip"');

        return $response;
    }

    private function statement() {
        $statements = Model\Statement::applyWindowConditions($this->window)->get();
        $headers = [__('Date'), __('Source'), __('Type'), __('Amount'), __('Description'), __('Balance')];
        $values = $statements->map(function($statement) {
            return [
                $statement->date,
                $statement->source,
                __($statement->type),
                $statement->amount,
                $statement->description,
                $statement->balance,
            ];
        });

        $handle = fopen($this->base_path.'/'.__('statement').'.csv', 'w');
        fputs($handle, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));
        fputcsv($handle, $headers, ',');

        foreach ( $values as $value ) {
            fputcsv($handle, $value, ',');
        }
        fclose($handle);

        $this->files[] = [
            'path' => $this->base_path.'/'.__('statement').'.csv',
            'subfolder' => '',
        ];
    }

    private function expenseInvoices() {
        $expense_invoices = Model\ExpenseInvoice::applyWindowConditions($this->window)->get();
        foreach ( $expense_invoices as $expense_invoice ) {
            $this->files[] = [
                'path' => config('path.uploads_path').$expense_invoice->path,
                'subfolder' => __('expenses'),
            ];
        }
    }

    private function invoices() {
        $invoices = Model\Invoice::with('client', 'contract', 'company', 'client_company', 'template')
            ->applyWindowConditions($this->window)
            ->get();

        foreach ( $invoices as $invoice ) {
            $pdf = Pdf::run($invoice);
            $pdf->render();

            $path = $this->base_path.'/'.$invoice->series.$invoice->number.'.pdf';
            file_put_contents($path, $pdf->output());
            $this->files[] = [
                'path' => $path,
                'subfolder' => __('invoices'),
            ];
        }
    }
}
