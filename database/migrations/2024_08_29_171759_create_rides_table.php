<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rides', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('rider_id')->nullable();
            $table->string('location_from');
            $table->string('location_to');
            $table->string('distance');
            $table->string('date')->nullable();
            $table->string('time')->nullable();
            $table->text('stops')->nullable();
            $table->string('pickup_location_lat');
            $table->string('pickup_location_lng');
            $table->string('dropoff_location_lat');
            $table->string('dropoff_location_lng');
            $table->string('status')->default('pending');
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
        Schema::dropIfExists('rides');
    }
};
