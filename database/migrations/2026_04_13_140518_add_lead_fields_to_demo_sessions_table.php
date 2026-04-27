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
        Schema::table('demo_sessions', function (Blueprint $table) {
            $table->string('lead_name')->nullable()->after('agent_id');
            $table->string('lead_email')->nullable()->after('lead_name');
            $table->string('lead_ip', 45)->nullable()->after('lead_email');
        });
    }

    public function down(): void
    {
        Schema::table('demo_sessions', function (Blueprint $table) {
            $table->dropColumn(['lead_name', 'lead_email', 'lead_ip']);
        });
    }
};
