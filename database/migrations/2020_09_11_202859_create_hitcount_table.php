<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeasuresTable extends Migration
{
    /**
    * Run the migrations.
    *
    * @return void
    */
    public function up()
    {
        Schema::create('hitcounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('domain');
            $table->string('item');
            $table->unsignedBigInteger('count');
            //$table->double('measure_value', 8, 2);
            $table->unsignedBigInteger('user_id');
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
        Schema::dropIfExists('measures');
    }
}