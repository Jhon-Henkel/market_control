<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('nfce', function (Blueprint $table) {
            $table->increments('id');
            $table->string('number', 10)->nullable(false);
            $table->string('series', 3)->nullable(false);
            $table->string('key', 44)->nullable(false);
            $table->string('emission_date', 19)->nullable(false);
            $table->tinyInteger('processed')->nullable(false)->default(0);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nfce');
    }
};
