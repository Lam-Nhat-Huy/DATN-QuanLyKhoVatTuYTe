<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inventory_check_details', function (Blueprint $table) {
            $table->id();
            $table->char('inventory_check_code', 20)->nullable();
            $table->char('equipment_code', 20)->nullable();
            $table->integer('current_quantity')->nullable();
            $table->integer('actual_quantity')->nullable();
            $table->integer('unequal')->nullable();
            $table->char('batch_number', 20);
            $table->text('equipment_note')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('inventory_check_code')->references('code')->on('inventory_checks')->onDelete('set null');
            $table->foreign('equipment_code')->references('code')->on('equipments')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventory_check_details');
    }
};