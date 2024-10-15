<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('exports', function (Blueprint $table) {
            $table->char('code', 20)->primary();
            $table->text('note')->nullable();
            $table->boolean('status')->default(true);
            $table->date('export_date')->nullable();
            $table->char('department_code', 20)->nullable();
            $table->char('export_request_code', 20)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('department_code')->references('code')->on('departments')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('exports');
    }
};
