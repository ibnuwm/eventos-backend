<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Blog CMS
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id')->nullable();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('featured_image')->nullable();
            $table->string('author')->default('EventOS');
            $table->json('tags')->nullable();
            $table->string('category')->default('tips');
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        // Vendor page views analytics
        Schema::create('vendor_page_views', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('vendor_id');
            $table->string('ip_address')->nullable();
            $table->string('referrer')->nullable();
            $table->string('city')->nullable();
            $table->timestamp('viewed_at')->useCurrent();
        });

        // Vendor leads from storefront
        Schema::create('vendor_storefront_leads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('vendor_id');
            $table->string('client_name');
            $table->string('client_whatsapp');
            $table->text('message')->nullable();
            $table->string('status')->default('new');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('blog_posts');
        Schema::dropIfExists('vendor_page_views');
        Schema::dropIfExists('vendor_storefront_leads');
    }
};
