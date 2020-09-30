<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPictureAndDescriptionShapeCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tab_categorias_shape', function (Blueprint $table) {
            $table->string('foto_categoria', 100)->nullable(false)->default("n/a");
            $table->text('descripcion_categoria')->nullable(false)->default("n/a");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tab_categorias_shape', function (Blueprint $table) {
            $table->dropColumn("foto_categoria");
            $table->dropColumn("descripcion_categoria");
        });
    }
}
