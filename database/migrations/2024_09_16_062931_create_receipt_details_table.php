<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('receipt_details', function (Blueprint $table) {
            $table->id();
            $table->char('receipt_code', 20)->nullable();
            $table->char('batch_number', 20)->nullable();
            $table->timestamp('expiry_date')->nullable();
            $table->timestamp('manufacture_date')->nullable();
            $table->integer('quantity');
            $table->decimal('VAT', 10, 2)->nullable();
            $table->decimal('discount', 10, 2)->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->char('equipment_code', 20)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('receipt_code')->references('code')->on('receipts')->onDelete('cascade');
            $table->foreign('equipment_code')->references('code')->on('equipments')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('receipt_details');
    }
};
