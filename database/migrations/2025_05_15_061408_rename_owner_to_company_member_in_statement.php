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
            $table->renameColumn('owner_id', 'company_member_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('statement', function (Blueprint $table) {
            $table->renameColumn('company_member_id', 'owner_id');
        });
    }
};
