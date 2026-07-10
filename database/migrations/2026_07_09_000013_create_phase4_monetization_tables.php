<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Vendor ad campaigns (PPC)
        Schema::create('vendor_ad_campaigns', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('vendor_id');
            $table->string('campaign_name');
            $table->decimal('daily_budget', 15, 2)->default(50000);
            $table->decimal('total_spent', 15, 2)->default(0);
            $table->integer('impressions')->default(0);
            $table->integer('clicks')->default(0);
            $table->string('status')->default('active');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->timestamps();
        });

        // Premium vendor profiles
        Schema::create('vendor_premium_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('vendor_id');
            $table->string('badge_type')->default('premium'); // premium, enterprise, verified
            $table->boolean('is_featured')->default(false);
            $table->integer('priority_score')->default(0);
            $table->date('subscription_start');
            $table->date('subscription_end');
            $table->timestamps();
        });

        // Sponsored content
        Schema::create('sponsored_contents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('vendor_id')->nullable();
            $table->string('title');
            $table->text('content');
            $table->string('type')->default('article'); // article, banner, video
            $table->string('target_url')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
        });

        // API keys for third-party integrations
        Schema::create('api_keys', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('name');
            $table->string('key', 64)->unique();
            $table->json('permissions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
        });

        // Tenant public registrations
        Schema::create('tenant_registrations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('company_name');
            $table->string('email')->unique();
            $table->string('whatsapp');
            $table->string('password');
            $table->string('status')->default('pending');
            $table->string('subscription_tier')->default('basic');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('vendor_ad_campaigns');
        Schema::dropIfExists('vendor_premium_profiles');
        Schema::dropIfExists('sponsored_contents');
        Schema::dropIfExists('api_keys');
        Schema::dropIfExists('tenant_registrations');
    }
};
