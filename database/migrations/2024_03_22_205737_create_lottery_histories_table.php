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
        Schema::create('lottery_histories', function (Blueprint $table) {
            $table->id();
            $table->string('year');
            $table->string('month');
            $table->string('open_at');
            $table->text('first');
            $table->text('first_near');
            $table->text('second');
            $table->text('third');
            $table->text('fourth');
            $table->text('fifth');
            $table->text('first_three_digit');
            $table->text('last_three_digit');
            $table->text('last_two_digit');
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
        Schema::dropIfExists('lottery_histories');
    }
};