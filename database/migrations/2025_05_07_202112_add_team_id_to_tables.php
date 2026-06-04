<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private $table_names = [
        'contract_annex', 'contract_template', 'contract_annex_template',
    ];


    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ( $this->table_names as $table_name ) {
            Schema::table($table_name, function (Blueprint $table) {
                $table->foreignId('team_id')->index()->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ( $this->table_names as $table_name ) {
            Schema::table($table_name, function (Blueprint $table) {
                $table->dropColumn('team_id');
            });
        }
    }
};
