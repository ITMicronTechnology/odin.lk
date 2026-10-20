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
        // One row per browser (identified by an anonymous cookie).
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip', 45)->nullable();
            $table->char('country_code', 2)->nullable();
            $table->string('country_name', 80)->nullable();
            $table->string('device_type', 10);
            $table->string('browser', 40);
            $table->string('os', 40);
            $table->unsignedInteger('visits_count')->default(0);
            $table->dateTime('first_seen_at');
            $table->dateTime('last_seen_at')->index();
            $table->timestamps();

            $table->index('ip');
        });

        // One row per visit (a session that ends after 30 minutes without activity).
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visitor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->char('country_code', 2)->nullable();
            $table->string('device_type', 10);
            $table->string('browser', 40);
            $table->string('os', 40);
            $table->string('landing_path');
            $table->string('referrer_host')->nullable();
            $table->unsignedInteger('page_views_count')->default(0);
            $table->dateTime('started_at')->index();
            $table->dateTime('last_activity_at')->index();
            $table->timestamps();
        });

        // One row per page view.
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('visitor_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('route_name')->nullable();
            $table->dateTime('created_at')->index();

            $table->index(['path', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_views');
        Schema::dropIfExists('visits');
        Schema::dropIfExists('visitors');
    }
};
