<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    public function up():void {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('source_account_id');
            $table->unsignedBigInteger('destination_account_id');
            $table->string('ip_address');
            $table->string('location');
            $table->decimal('amount', 15, 2);
            $table->boolean('status')->default('pending');
            $table->string('reason')->nullable();
            $table->timestamps();

            $table->foreign('source_account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('destination_account_id')->references('id')->on('accounts')->onDelete('cascade');
        });
    }

    public function down():void {
        Schema::dropIfExists('transactions');
    }
}
