<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoice', function (Blueprint $table) {
            $table->id();
            $table->integer('team_id')->index();
            $table->integer('client_id')->index();
            $table->integer('contract_id')->index()->nullable();
            $table->integer('company_id')->index();
            $table->integer('client_company_id')->index();
            $table->integer('template_id')->index();
            $table->string('series');
            $table->string('number');
            $table->date('date');
            $table->date('due_date');
            $table->text('lines');
            $table->float('vat');
            $table->float('total');
            $table->float('total_with_vat');
            $table->float('total_received');
            $table->string('currency');
            $table->string('conversion_rate')->nullable();
            $table->date('conversion_rate_date')->nullable();
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice');
    }
};
