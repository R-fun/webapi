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
        Schema::create('barang_history',function(Blueprint $table){
            $table->id();
            $table->bigInteger('barang_id')->unsigned();
            $table->string('harga_barang',100)->nullable();
            $table->integer('angkasatuan')->unsigned()->nullable();
            $table->bigInteger('satuan')->unsigned()->nullable();
            $table->string('operation',100);
            $table->string('keterangan',100);
            $table->string('json_data')->nullable();
            $table->bigInteger('user_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('barang_history',function(Blueprint $table){
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('satuan')->references('id')->on('satuan')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('barang_history');
    }
};
