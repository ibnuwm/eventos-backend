<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('name');
            $table->string('whatsapp');
            $table->string('email')->nullable();
            $table->date('event_date')->nullable();
            $table->integer('pax_count')->default(500);
            $table->decimal('budget_estimation', 15, 2)->default(0);
            $table->string('status')->default('new'); // new, quotation_sent, negotiation, won, lost
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('lead_id')->nullable();
            $table->string('title');
            $table->string('client_name');
            $table->date('event_date');
            $table->string('venue_name')->nullable();
            $table->decimal('contract_value', 15, 2)->default(0);
            $table->decimal('vendor_cost', 15, 2)->default(0);
            $table->decimal('operational_cost', 15, 2)->default(0);
            $table->string('payment_status')->default('dp_30'); // booking_fee, dp_30, dp_80, fully_paid
            $table->integer('days_remaining')->default(30);
            $table->integer('progress_percentage')->default(0);
            $table->timestamps();
        });

        Schema::create('project_tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('project_id');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->string('division'); // Photography, Decoration, Catering, Sound & MC
            $table->string('title');
            $table->date('due_date');
            $table->boolean('is_completed')->default(false);
            $table->string('assigned_vendor_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_tasks');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('leads');
    }
};
