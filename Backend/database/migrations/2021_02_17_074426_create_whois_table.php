<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhoisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('whois', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('createdDate');
            $table->string('updatedDate');
            $table->string('expiresDate');
            $table->string('registrant');
            $table->string('domainName');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('whois');
    }
}
