<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── pages table ───────────────────────────────────────────────────────
        Schema::table('pages', function (Blueprint $table) {
            // Identifier for system/predefined pages (null = custom page)
            $table->string('key')->nullable()->unique()->after('slug');
            // GrapesJS rendered output
            $table->longText('html')->nullable()->after('content');
            $table->longText('css')->nullable()->after('html');
            // GrapesJS project JSON (for re-loading the editor state)
            $table->longText('gjs_data')->nullable()->after('css');
        });

        // ── admins table ──────────────────────────────────────────────────────
        Schema::table('admins', function (Blueprint $table) {
            $table->boolean('is_super_admin')->default(false)->after('email');
        });

        // Mark the first admin as super admin (SaaS owner)
        DB::table('admins')->where('id', 1)->update(['is_super_admin' => true]);
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['key', 'html', 'css', 'gjs_data']);
        });

        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('is_super_admin');
        });
    }
};
