<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bets', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('register_id')->nullable();
            $table->foreign('register_id')->references('id')->on('registers')->onDelete('set null');

            $table->string('ref')->nullable();
            $table->json('other')->nullable();

            $table->integer('total')->default(0);

            $table->longText('voucher')->nullable();
            $table->timestamps();
        });

        Schema::create('bet_numbers', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('bet_id')->nullable();
            $table->foreign('bet_id')->references('id')->on('bets')->onDelete('set null');

            $table->unsignedBigInteger('number_id')->nullable();
            $table->foreign('number_id')->references('id')->on('numbers')->onDelete('set null');

            $table->string('type')->nullable();
            $table->integer('amount')->default(0);

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
        Schema::dropIfExists('bet_numbers');
        Schema::dropIfExists('bets');
    }
}
