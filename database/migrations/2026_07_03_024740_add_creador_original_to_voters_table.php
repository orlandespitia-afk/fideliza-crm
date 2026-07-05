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
        Schema::table('voter_records', function (Blueprint $table) {
            $table->string('creador_original_nombre')
                ->nullable()
                ->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voter_records', function (Blueprint $table) {
            $table->dropColumn('creador_original_nombre');
        });
    }
};
