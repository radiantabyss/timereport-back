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
        Schema::create('statement', function (Blueprint $table) {
            $table->id();
            $table->integer('team_id')->index();
            $table->integer('invoice_id')->index()->nullable();
            $table->integer('expense_id')->index()->nullable();
            $table->integer('withdrawal_id')->index()->nullable();
            $table->string('type')->index();
            $table->string('source')->index();
            $table->string('description', 500)->nullable();
            $table->float('amount');
            $table->float('balance');
            $table->date('date')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statement');
    }
};
