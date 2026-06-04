<?php
namespace App\Models;

class Invoice extends Model
{
    use Traits\TeamExclusivityWithClient;
    use Traits\WindowConditions;

    protected $table = 'invoice';

    public function client() {
        return $this->belongsTo(Client::class)->withoutGlobalScope('team_exclusivity');
    }

    public function contract() {
        return $this->belongsTo(Contract::class)->withoutGlobalScope('team_exclusivity');
    }

    public function company() {
        return $this->belongsTo(Company::class)->withoutGlobalScope('team_exclusivity');
    }

    public function client_company() {
        return $this->belongsTo(Company::class, 'client_company_id')->withoutGlobalScope('team_exclusivity');
    }

    public function template() {
        return $this->belongsTo(InvoiceTemplate::class)->withoutGlobalScope('team_exclusivity');
    }
}
