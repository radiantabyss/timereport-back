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
        Schema::table('statement', function (Blueprint $table) {
            $table->boolean('is_tax')->nullable()->after('balance');
            $table->boolean('is_salary')->nullable()->after('balance');
            $table->foreignId('owner_id')->index()->nullable()->after('balance');
            $table->foreignId('supplier_id')->index()->nullable()->after('balance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('statement', function (Blueprint $table) {
            $table->dropColumn('is_tax');
            $table->dropColumn('is_salary');
            $table->dropColumn('owner_id');
            $table->dropColumn('supplier_id');
        });
    }
};
