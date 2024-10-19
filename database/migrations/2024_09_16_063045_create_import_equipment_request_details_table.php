<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('import_equipment_request_details', function (Blueprint $table) {
            $table->id();
            $table->char('import_request_code', 20)->nullable();
            $table->char('equipment_code', 20)->nullable();
            $table->integer('quantity');
            $table->decimal('price')->nullable();
            $table->boolean('status')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('import_request_code')->references('code')->on('import_equipment_requests')->onDelete('cascade');
            $table->foreign('equipment_code')->references('code')->on('equipments')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('import_equipment_request_details');
    }
};
