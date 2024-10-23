<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('quote_histories', function (Blueprint $table) {
            $table->id();
            $table->char('supplier_code', 10);
            $table->string('file_excel', 255);
            $table->char('user_code', 20)->nullable();
            $table->timestamps();

            $table->foreign('user_code')->references('code')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('quote_histories');
    }
};
