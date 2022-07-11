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
        Schema::table('barang_history', function (Blueprint $table) {
            $table->unsignedBigInteger('toko_id')->nullable();
        });

        Schema::table('barang_history', function (Blueprint $table) {
            $table->foreign('toko_id')->references('id')->on('toko')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('barang_history', function (Blueprint $table) {
            //
        });
    }
};
