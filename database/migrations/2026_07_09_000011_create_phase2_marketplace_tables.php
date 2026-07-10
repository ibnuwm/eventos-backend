<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Consumer wishlists (favorite vendors)
        Schema::create('wishlists', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('session_id'); // guest session identifier
            $table->uuid('vendor_id');
            $table->timestamps();
            $table->unique(['session_id', 'vendor_id']);
        });

        // Inspiration boards
        Schema::create('inspiration_boards', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('session_id');
            $table->string('title')->default('Inspirasi Saya');
            $table->json('items')->nullable(); // array of {vendor_id, image_url, note}
            $table->timestamps();
        });

        // Reviews & ratings
        Schema::create('vendor_reviews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('vendor_id');
            $table->string('reviewer_name');
            $table->string('reviewer_whatsapp')->nullable();
            $table->tinyInteger('rating')->unsigned(); // 1-5
            $table->text('comment')->nullable();
            $table->string('photo_url')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });

        // Virtual expo events
        Schema::create('virtual_expos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('event_date');
            $table->date('registration_end')->nullable();
            $table->string('status')->default('upcoming');
            $table->timestamps();
        });

        // Virtual expo vendor booths
        Schema::create('virtual_expo_booths', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('expo_id');
            $table->uuid('vendor_id');
            $table->string('booth_title')->nullable();
            $table->text('description')->nullable();
            $table->string('video_url')->nullable();
            $table->json('gallery')->nullable();
            $table->integer('visitor_count')->default(0);
            $table->integer('lead_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('inspiration_boards');
        Schema::dropIfExists('vendor_reviews');
        Schema::dropIfExists('virtual_expos');
        Schema::dropIfExists('virtual_expo_booths');
    }
};
