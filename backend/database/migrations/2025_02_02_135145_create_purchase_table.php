<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('purchase', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('nfce_id')->nullable(false);
            $table->decimal('subtotal_value')->nullable(false);
            $table->decimal('discount_value')->nullable(false);
            $table->decimal('total_value')->nullable(false);
            $table->integer('total_items')->nullable(false);
            $table->string('purchase_date', 19)->nullable(false);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->foreign('nfce_id')->references('id')->on('nfce');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase');
    }
};
