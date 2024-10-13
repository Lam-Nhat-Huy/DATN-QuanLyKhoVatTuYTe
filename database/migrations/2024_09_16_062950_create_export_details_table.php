<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('export_details', function (Blueprint $table) {
            $table->id();
            $table->char('export_code', 20)->nullable();
            $table->char('equipment_code', 20)->nullable();
            $table->integer('quantity');
            $table->char('batch_number', 20)->nullable();

            $table->foreign('export_code')->references('code')->on('exports')->onDelete('set null');
            $table->foreign('equipment_code')->references('code')->on('equipments')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('export_details');
    }
};
