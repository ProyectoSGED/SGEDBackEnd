<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AddEmailAndResetPasswordToTabUsuariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tab_usuarios', function (Blueprint $table) {
            $table->string('mail_usuario', 100)->nullable(false)->default(Str::random(20))->unique();
            $table->boolean("cambiar_password")->nullable(false)->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tab_usuarios', function (Blueprint $table) {
            //
        });
    }
}
