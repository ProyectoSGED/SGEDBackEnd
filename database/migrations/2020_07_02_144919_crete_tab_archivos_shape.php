<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreteTabArchivosShape extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tab_archivos_shape', function (Blueprint $table) {
            $table->integer('id_archivo_shape')->nullable(false)->autoIncrement();
            $table->integer('id_shape')->nullable(false);
            $table->text('ruta_archivo_shape')->nullable(false);

            $table->foreign('id_shape')->references('id_shape')->on('tab_shape');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tab_archivos_shape');
    }
}
