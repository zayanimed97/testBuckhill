<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Orders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->foreignId('user_id');
            $table->foreignId('order_status_id');
            $table->foreignId('payment_id');
            $table->json('products');
            $table->json('address');
            $table->float('delivery_fee')->nullable();
            $table->float('amount');
            $table->timestamp('shipped_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
