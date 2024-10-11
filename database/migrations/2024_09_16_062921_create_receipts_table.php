<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->char('code', 20)->primary();
            $table->char('supplier_code', 20);
            $table->text('note')->nullable();
            $table->boolean('status')->default(false);
            $table->char('receipt_no');
            $table->timestamp('receipt_date')->nullable();
            $table->char('created_by', 20);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('receipts');
    }
};
