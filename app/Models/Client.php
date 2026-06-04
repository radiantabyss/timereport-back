<?php
namespace App\Models;

class Client extends Model
{
    use Traits\TeamExclusivityWithClient;

    protected $table = 'client';

    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function default_invoice_template() {
        return $this->belongsTo(InvoiceTemplate::class, 'default_invoice_template_id');
    }
}
