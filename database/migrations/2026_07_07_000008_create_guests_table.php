<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('guests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('project_id');
            $table->string('name');
            $table->string('whatsapp')->nullable();
            $table->string('category')->default(' umum'); // VIP, Keluarga, Umum
            $table->integer('guest_count')->default(1);
            $table->string('rsvp_status')->default('pending'); // pending, confirmed, declined
            $table->text('menu_choice')->nullable();
            $table->text('notes')->nullable();
            $table->string('table_number')->nullable();
            $table->string('token', 64)->unique()->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('guests');
    }
};
