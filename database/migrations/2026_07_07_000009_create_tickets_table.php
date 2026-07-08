<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('event_tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('project_id');
            $table->string('event_title');
            $table->date('event_date');
            $table->string('venue');
            $table->text('description')->nullable();
            $table->timestamps();
        });
        Schema::create('ticket_tiers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('event_ticket_id');
            $table->string('tier_name'); // Early Bird, Regular, VIP
            $table->decimal('price', 15, 2);
            $table->integer('quota');
            $table->integer('sold')->default(0);
            $table->timestamps();
        });
        Schema::create('ticket_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tier_id');
            $table->string('buyer_name');
            $table->string('buyer_email')->nullable();
            $table->string('buyer_whatsapp');
            $table->integer('quantity');
            $table->decimal('total', 15, 2);
            $table->string('status')->default('pending'); // pending, paid, used, cancelled
            $table->string('qr_token', 64)->unique()->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('ticket_orders');
        Schema::dropIfExists('ticket_tiers');
        Schema::dropIfExists('event_tickets');
    }
};
