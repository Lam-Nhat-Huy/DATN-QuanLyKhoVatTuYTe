<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->char('code', 20)->primary();
            $table->string('first_name', 30);
            $table->string('last_name', 15);
            $table->string('avatar', 255)->nullable();
            $table->string('email', 255)->unique();
            $table->string('phone', 11)->unique();
            $table->string('password', 255);
            $table->date('birth_day')->nullable();
            $table->string('gender', 5);
            $table->string('address', 255)->nullable();
            $table->boolean('isAdmin')->default(false);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('sessions');
    }
};
