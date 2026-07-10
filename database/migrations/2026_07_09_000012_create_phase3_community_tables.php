<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Forum / community discussion topics
        Schema::create('forum_topics', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('content');
            $table->string('author_name');
            $table->string('author_whatsapp')->nullable();
            $table->string('category')->default('umum');
            $table->integer('view_count')->default(0);
            $table->integer('reply_count')->default(0);
            $table->boolean('is_pinned')->default(false);
            $table->timestamps();
        });

        // Forum replies
        Schema::create('forum_replies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('topic_id');
            $table->text('content');
            $table->string('author_name');
            $table->string('author_whatsapp')->nullable();
            $table->timestamps();
        });

        // Vendor collaborations (tagging between vendors on projects)
        Schema::create('vendor_collaborations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('vendor_id');
            $table->uuid('collaborator_id');
            $table->string('project_title')->nullable();
            $table->string('photo_url')->nullable();
            $table->timestamps();
        });

        // UGC gallery (user-submitted wedding/event photos)
        Schema::create('ugc_galleries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('session_id');
            $table->string('uploader_name');
            $table->string('photo_url')->nullable();
            $table->text('caption')->nullable();
            $table->json('tagged_vendor_ids')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('forum_topics');
        Schema::dropIfExists('forum_replies');
        Schema::dropIfExists('vendor_collaborations');
        Schema::dropIfExists('ugc_galleries');
    }
};
