<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTabUsuariosPerfiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tab_usuario_perfiles', function (Blueprint $table) {
            $table->integer('id_usuario_perfil')->nullable(false)->autoIncrement();
            $table->integer('id_usuario')->nullable(false);
            $table->integer('id_perfil')->nullable(false);

            $table->foreign('id_perfil')->references('id_perfil')->on('tab_perfiles');
            $table->foreign('id_usuario')->references('id_usuario')->on('tab_usuarios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tab_usuarios_perfiles');
    }
}
