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
        Schema::create('two_d_results', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->string('set');
            $table->string('val');
            $table->enum('time_type',['AM','PM']);
            $table->string('date');
            $table->enum('country',['Thai','UK']);
            $table->integer('status')->default(1)->nullable();
            $table->integer('serial')->default(1)->nullable();
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
        Schema::dropIfExists('two_d_results');
    }
};
