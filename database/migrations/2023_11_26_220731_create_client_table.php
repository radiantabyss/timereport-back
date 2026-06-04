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
        Schema::create('client', function (Blueprint $table) {
            $table->id();
            $table->integer('team_id')->index();
            $table->integer('company_id')->index()->nullable();
            $table->integer('default_invoice_template_id')->index()->nullable();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->string('default_currency', 20)->nullable();
            $table->tinyInteger('default_vat')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client');
    }
};
