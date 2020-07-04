<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreteTabUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tab_usuarios', function (Blueprint $table) {
            $table->integer('id_usuario')->nullable(false)->autoIncrement();
            $table->string('nombre_usuario', 100)->nullable(false)->unique();
            $table->string('primer_nombre', 100)->nullable(false);
            $table->string('primer_apellido', 100)->nullable(false);
            $table->text('password')->nullable(false);
            $table->boolean("usuario_activo")->nullable(false)->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tab_usuarios');
    }
}
