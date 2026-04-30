<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('license_buyers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('license_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('license_buyer_id')->constrained()->cascadeOnDelete();
            $table->string('key', 29)->unique();
            $table->enum('status', ['active', 'revoked', 'expired'])->default('active');
            $table->tinyInteger('max_domains')->default(5);
            $table->text('notes')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('license_domains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('license_key_id')->constrained()->cascadeOnDelete();
            $table->string('domain');
            $table->timestamp('first_seen');
            $table->timestamp('last_seen');
            $table->unsignedInteger('verification_count')->default(0);
            $table->unique(['license_key_id', 'domain']);
        });

        Schema::create('license_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('license_key_id')->nullable()->constrained()->nullOnDelete();
            $table->string('domain');
            $table->string('ip', 45);
            $table->enum('result', ['success', 'invalid_key', 'revoked', 'expired', 'domain_limit_reached']);
            $table->timestamp('verified_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('license_verifications');
        Schema::dropIfExists('license_domains');
        Schema::dropIfExists('license_keys');
        Schema::dropIfExists('license_buyers');
    }
};
