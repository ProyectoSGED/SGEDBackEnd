<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTabShape extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tab_shape', function (Blueprint $table) {
            $table->integer('id_shape')->primary()->nullable(false);
            $table->text('nombre_shape')->nullable(false)->unique();
            $table->text('resumen_shape')->nullable(false);
            $table->string('autor', 100)->nullable(false);
            $table->date('fecha_publicacion')->nullable(false);
            $table->date('fecha_creacion_metadato')->nullable(false);
            $table->integer('id_categoria')->nullable(false);

            $table->foreign('id_categoria')->references('id_categoria')->on('tab_categorias_shape');
        });
    }

    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tab_shape');
    }
}
