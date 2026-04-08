<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('brand_settings', function (Blueprint $table) {
            $table->string('website_url', 255)->nullable()->after('favicon_path');
            $table->string('instagram_url', 255)->nullable()->after('website_url');
            $table->string('facebook_url', 255)->nullable()->after('instagram_url');
            $table->string('twitter_url', 255)->nullable()->after('facebook_url');
            $table->string('linkedin_url', 255)->nullable()->after('twitter_url');
        });
    }

    public function down(): void
    {
        Schema::table('brand_settings', function (Blueprint $table) {
            $table->dropColumn(['website_url', 'instagram_url', 'facebook_url', 'twitter_url', 'linkedin_url']);
        });
    }
};
