<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('temp_order_details', function (Blueprint $table) {
            $table->id();
            $table->string('item_id');
            $table->string('user_id');
            $table->decimal('rent_or_sale_price',8,2)->nullable();
            $table->boolean('trouser')->default(0);
            $table->boolean('coat')->default(0);
            $table->boolean('west')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_order_details');
    }
};
