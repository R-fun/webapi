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
        Schema::table('toko', function (Blueprint $table) {
            $table->dropColumn('daerah');
            $table->string('alamat')->nullable();
            $table->bigInteger('jenis_toko')->unsigned()->change();
            $table->dropColumn('pemilik');
            $table->bigInteger('user_id')->unsigned();

            //relasi
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('jenis_toko')->references('id')->on('jenis_toko')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('toko', function (Blueprint $table) {
            //
        });
    }
};
