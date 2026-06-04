<?php
namespace App\Models;

class ExpenseInvoice extends Model
{
    use Traits\TeamExclusivity;
    use Traits\WindowConditions;

    protected $table = 'expense_invoice';
}
