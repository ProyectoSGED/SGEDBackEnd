<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class TabUser extends Model
{
    protected $table = "tab_usuarios";
    protected $primaryKey ="id_usuario";

    public $timestamps = false;
}
