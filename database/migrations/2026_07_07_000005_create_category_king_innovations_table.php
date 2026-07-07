<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Escrow Split Accounts & Disbursements
        Schema::create('escrow_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('project_id');
            $table->uuid('invoice_id')->nullable();
            $table->decimal('total_payment_received', 15, 2);
            $table->decimal('vendor_hpp_escrow_holding', 15, 2); // 65%
            $table->decimal('wo_margin_released', 15, 2); // 35%
            $table->string('status')->default('holding'); // holding, partially_released, fully_released
            $table->timestamps();
        });

        Schema::create('escrow_disbursements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('escrow_account_id');
            $table->foreign('escrow_account_id')->references('id')->on('escrow_accounts')->onDelete('cascade');
            $table->uuid('vendor_id');
            $table->string('vendor_name');
            $table->string('bank_account_info');
            $table->decimal('disbursement_amount', 15, 2);
            $table->uuid('triggered_by_task_id')->nullable();
            $table->string('status')->default('pending'); // pending, disbursed
            $table->timestamp('disbursed_at')->nullable();
            $table->timestamps();
        });

        // 2. Interactive Floorplans & Seating Studio
        Schema::create('floorplans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('project_id');
            $table->string('title');
            $table->integer('canvas_width_meters')->default(30);
            $table->integer('canvas_height_meters')->default(20);
            $table->integer('total_tables')->default(0);
            $table->integer('total_seats_allocated')->default(0);
            $table->timestamps();
        });

        Schema::create('floorplan_elements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('floorplan_id');
            $table->foreign('floorplan_id')->references('id')->on('floorplans')->onDelete('cascade');
            $table->string('element_type'); // round_table, rectangular_table, vip_stage, sound_booth, exit_door
            $table->string('label'); // e.g. "Table 1 (Family VIP)"
            $table->float('pos_x')->default(100);
            $table->float('pos_y')->default(100);
            $table->integer('seat_capacity')->default(8);
            $table->integer('seats_occupied')->default(0);
            $table->timestamps();
        });

        // 3. Automated Technical Riders
        Schema::create('technical_riders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('project_id');
            $table->string('document_number')->unique();
            $table->integer('minimum_power_kva')->default(35); // 35,000 VA
            $table->boolean('requires_genset_backup')->default(true);
            $table->string('lighting_color_temp')->default('4000K Natural White');
            $table->string('loading_curfew')->default('06.00 WIB');
            $table->decimal('curfew_penalty_per_hour', 15, 2)->default(5000000);
            $table->string('status')->default('compiled'); // compiled, signed_by_venue
            $table->timestamps();
        });

        // 4. B2B Supply Chain Pooling (Group Buying Network)
        Schema::create('group_buying_pools', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('item_name'); // e.g. "Mawar Putih Avalanche Grade A"
            $table->string('category'); // Floral, Lighting, Furniture
            $table->date('target_weekend_date');
            $table->decimal('retail_price_per_unit', 15, 2);
            $table->decimal('wholesale_price_per_unit', 15, 2);
            $table->integer('minimum_pool_qty')->default(1000);
            $table->integer('current_pooled_qty')->default(0);
            $table->string('supplier_name');
            $table->string('status')->default('open'); // open, threshold_reached, ordered
            $table->timestamps();
        });

        Schema::create('group_buying_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('pool_id');
            $table->foreign('pool_id')->references('id')->on('group_buying_pools')->onDelete('cascade');
            $table->uuid('tenant_id');
            $table->uuid('vendor_id');
            $table->string('vendor_name');
            $table->integer('order_qty');
            $table->decimal('total_savings_idr', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_buying_orders');
        Schema::dropIfExists('group_buying_pools');
        Schema::dropIfExists('technical_riders');
        Schema::dropIfExists('floorplan_elements');
        Schema::dropIfExists('floorplans');
        Schema::dropIfExists('escrow_disbursements');
        Schema::dropIfExists('escrow_accounts');
    }
};
