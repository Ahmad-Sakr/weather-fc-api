<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('record_id')->nullable()->constrained('records','id')->onDelete('cascade');
            $table->time('hour')->nullable();
            $table->string('cloud')->nullable();
            $table->string('temp')->nullable();
            $table->string('min_temp')->nullable();
            $table->string('max_temp')->nullable();
            $table->string('pressure')->nullable();
            $table->string('sea_level')->nullable();
            $table->string('humidity')->nullable();
            $table->string('wind_speed')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('details');
    }
}
