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
        Schema::create('timereport', function (Blueprint $table) {
            $table->id();
            $table->integer('team_id')->index();
            $table->integer('user_id')->index();
            $table->integer('client_id')->index();
            $table->integer('project_id')->index();
            $table->integer('invoice_id')->index()->nullable();
            $table->date('date');
            $table->float('hours');
            $table->float('rate');
            $table->float('tax_percent')->default(0);
            $table->string('currency', 10);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timereport');
    }
};
