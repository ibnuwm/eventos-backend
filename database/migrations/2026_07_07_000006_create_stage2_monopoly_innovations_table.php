<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Embedded Working Capital Loans (EventOS PayLater untuk Vendor)
        Schema::create('working_capital_loans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('vendor_id');
            $table->string('vendor_name');
            $table->uuid('escrow_account_id');
            $table->decimal('contract_hpp_value', 15, 2);
            $table->decimal('loan_amount_requested', 15, 2);
            $table->decimal('platform_fee_percentage', 5, 2)->default(1.50);
            $table->decimal('net_disbursement_idr', 15, 2);
            $table->string('status')->default('active'); // active, repaid_from_escrow
            $table->timestamps();
        });

        // 2. IoT QR Asset Tracking & Damage Claims
        Schema::create('asset_qr_trackings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('inventory_item_id');
            $table->string('item_name');
            $table->string('qr_barcode')->unique();
            $table->uuid('project_id');
            $table->string('scan_out_time')->nullable();
            $table->string('scan_in_time')->nullable();
            $table->string('current_location')->default('Warehouse');
            $table->string('condition_status')->default('good'); // good, damaged
            $table->timestamps();
        });

        Schema::create('damage_claims', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('asset_qr_id');
            $table->uuid('project_id');
            $table->string('photo_evidence_url');
            $table->string('ai_damage_assessment'); // e.g. "Severe Lens Crack - 85% Damage"
            $table->decimal('deduction_amount_idr', 15, 2);
            $table->string('status')->default('claimed_from_deposit');
            $table->timestamps();
        });

        // 3. StageCommand Live Show-Caller Console Cues
        Schema::create('stage_cues', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('project_id');
            $table->string('cue_number'); // e.g. "CUE-14"
            $table->string('moment_title'); // e.g. "Grand Entrance & First Dance"
            $table->string('target_divisions'); // "Lighting, Sound, MC, Video"
            $table->integer('countdown_seconds')->default(5);
            $table->string('status')->default('standby'); // standby, live_executing, completed
            $table->timestamps();
        });

        // 4. AI Compliance & Anti-Scam Portfolio Audits
        Schema::create('vendor_compliance_audits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('vendor_id');
            $table->string('vendor_name');
            $table->boolean('legal_npwp_verified')->default(true);
            $table->integer('reverse_image_authenticity_score')->default(100); // 100% original
            $table->string('verification_badge')->default('Enterprise Blue Shield Verified');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_compliance_audits');
        Schema::dropIfExists('stage_cues');
        Schema::dropIfExists('damage_claims');
        Schema::dropIfExists('asset_qr_trackings');
        Schema::dropIfExists('working_capital_loans');
    }
};
