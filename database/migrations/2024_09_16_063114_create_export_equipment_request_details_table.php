<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('export_equipment_request_details', function (Blueprint $table) {
            $table->id();
            $table->char('export_request_code', 20)->nullable();
            $table->char('equipment_code', 20)->nullable();
            $table->integer('quantity');
            $table->boolean('status')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('export_request_code')->references('code')->on('export_equipment_requests')->onDelete('cascade');
            $table->foreign('equipment_code')->references('code')->on('equipments')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('export_equipment_request_details');
    }
};
