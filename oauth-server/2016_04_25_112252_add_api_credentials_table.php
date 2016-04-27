<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddApiCredentialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_server_keys', function(Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->string('api_key');
            $table->string('api_secret');
            $table->string('origin');            
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
        Schema::drop('api_server_keys');
    }
}
