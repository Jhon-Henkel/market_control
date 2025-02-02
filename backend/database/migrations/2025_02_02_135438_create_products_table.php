<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('purchase_id')->nullable(false);
            $table->string('name', 255)->nullable(false);
            $table->decimal('quantity', 10, 3)->nullable(false);
            $table->string('unit', 10)->nullable(false);
            $table->decimal('unit_price')->nullable(false);
            $table->decimal('total_price')->nullable(false);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->foreign('purchase_id')->references('id')->on('purchase');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
