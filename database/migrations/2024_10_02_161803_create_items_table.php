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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('coat_no');
            $table->string('name');
            $table->string('material')->nullable();
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->string('image')->nullable();
            $table->string('description')->nullable();
            $table->boolean('status')->default(1);
            $table->boolean('trouser')->default(0);
            $table->boolean('coat')->default(0);
            $table->boolean('west')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
