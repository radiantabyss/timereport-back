<?php
namespace App\Models;

class InvoiceTemplate extends Model
{
    use Traits\TeamExclusivity;

    protected $table = 'invoice_template';
}
