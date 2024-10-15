<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->char('code', 20)->primary();
            $table->char('equipment_code', 20)->nullable();
            $table->char('batch_number', 20)->unique();
            $table->integer('current_quantity')->nullable();
            $table->timestamp('expiry_date')->nullable();
            $table->timestamp('manufacture_date')->nullable();
            $table->char('import_code', 20)->nullable();
            $table->char('export_code', 20)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('import_code')->references('code')->on('receipts')->onDelete('set null');
            $table->foreign('export_code')->references('code')->on('exports')->onDelete('set null');
            $table->foreign('equipment_code')->references('code')->on('equipments')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventories');
    }
};
