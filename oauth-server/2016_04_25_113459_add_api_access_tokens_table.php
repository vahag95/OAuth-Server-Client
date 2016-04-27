<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddApiAccessTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_server_access_tokens', function(Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->string('api_key_id');
            $table->string('access_token');
            $table->string('refresh_token');            
            $table->date('expire_at');            
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
        Schema::drop('api_server_access_tokens');
    }
}
