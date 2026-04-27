<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('demo_sessions', function (Blueprint $table) {
            $table->enum('type', ['self_service', 'invited'])
                ->default('self_service')
                ->after('token');
        });

        // Backfill any existing rows
        DB::table('demo_sessions')->update(['type' => 'self_service']);
    }

    public function down(): void
    {
        Schema::table('demo_sessions', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
