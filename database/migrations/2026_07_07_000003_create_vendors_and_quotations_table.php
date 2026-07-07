<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('name');
            $table->string('category'); // Photography, Decoration, Sound & Lighting, Catering
            $table->string('pic_name');
            $table->string('whatsapp');
            $table->decimal('rating', 3, 2)->default(5.00);
            $table->decimal('sla_punctuality', 5, 2)->default(99.00); // 99.2%
            $table->decimal('starting_price', 15, 2)->default(0);
            $table->string('area');
            $table->string('npwp')->nullable();
            $table->text('bank_account_info')->nullable();
            $table->timestamps();
        });

        Schema::create('quotations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('lead_id')->nullable();
            $table->string('title');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->string('status')->default('draft'); // draft, sent, approved
            $table->timestamps();
        });

        Schema::create('quotation_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('quotation_id');
            $table->foreign('quotation_id')->references('id')->on('quotations')->onDelete('cascade');
            $table->string('category');
            $table->string('title');
            $table->string('vendor_name');
            $table->decimal('price', 15, 2)->default(0);
            $table->boolean('is_optional')->default(false);
            $table->boolean('is_selected')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('quotations');
        Schema::dropIfExists('vendors');
    }
};
