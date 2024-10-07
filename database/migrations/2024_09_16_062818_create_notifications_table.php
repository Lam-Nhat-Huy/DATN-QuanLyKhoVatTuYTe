<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->char('code', 20)->primary();
            $table->char('user_code', 20)->nullable();
            $table->bigInteger('notification_type')->unsigned()->nullable();
            $table->text('content');
            $table->boolean('important')->default(0)->nullable();
            $table->boolean('status')->default(0);
            $table->boolean('lock_warehouse')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_code')->references('code')->on('users')->onDelete('set null');
            $table->foreign('notification_type')->references('id')->on('notification_types')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};
