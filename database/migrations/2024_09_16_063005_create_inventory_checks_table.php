<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inventory_checks', function (Blueprint $table) {
            $table->char('code', 20)->primary();
            $table->char('user_code', 20)->nullable();
            $table->char('recheck_user_code', 20)->nullable();
            $table->date('check_date')->nullable();
            $table->text('note')->nullable();
            $table->boolean('status')->default(true);
            $table->integer('check_count')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_code')->references('code')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventory_checks');
    }
};