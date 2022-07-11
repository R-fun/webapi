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
        Schema::create('barang_toko',function(Blueprint $table){
            $table->id();
            $table->bigInteger('barang_id')->unsigned();
            $table->bigInteger('toko_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('barang_toko',function(Blueprint $table){
            $table->foreign('barang_id')->references('id')->on('barang')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('toko_id')->references('id')->on('toko')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
