<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('project_id')->nullable();
            $table->string('invoice_number')->unique();
            $table->string('termin_type'); // DP_30, DP_50, PELUNASAN
            $table->decimal('amount', 15, 2);
            $table->string('status')->default('unpaid'); // unpaid, paid, cancelled
            $table->string('payment_gateway_ref')->nullable(); // Midtrans/Xendit token
            $table->timestamps();
        });

        Schema::create('rundown_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('project_id')->nullable();
            $table->string('time_slot');
            $table->integer('duration_minutes')->default(60);
            $table->string('activity_title');
            $table->string('division_pic');
            $table->text('notes')->nullable();
            $table->integer('sequence_order')->default(0);
            $table->timestamps();
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('project_id')->nullable();
            $table->string('channel'); // #dekorasi-layout
            $table->string('sender_name');
            $table->string('sender_role');
            $table->text('text');
            $table->timestamps();
        });

        Schema::create('file_assets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('project_id')->nullable();
            $table->string('name');
            $table->string('folder'); // /Contracts, /CAD_Layouts
            $table->string('storage_path'); // MinIO s3 path
            $table->string('size')->default('1 MB');
            $table->string('uploaded_by');
            $table->timestamps();
        });

        Schema::create('inventory_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('name');
            $table->string('category');
            $table->integer('total_stock')->default(100);
            $table->date('booked_for_date');
            $table->integer('allocated_qty')->default(0);
            $table->string('conflicting_project')->nullable();
            $table->boolean('has_conflict')->default(false);
            $table->timestamps();
        });

        Schema::create('staff_crews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('name');
            $table->string('role');
            $table->string('assigned_event_title');
            $table->string('check_in_time')->nullable();
            $table->string('location');
            $table->string('status')->default('standby'); // checked_in, on_way, standby
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_crews');
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('file_assets');
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('rundown_items');
        Schema::dropIfExists('invoices');
    }
};
