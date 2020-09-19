<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function __construct()
    {
        $this
            ->middleware(
                'auth:api',
                [
                    'except'=>['signin']
                ]
            );
    }

    public function signin(Request $request)
    {
        if (!$token = Auth::attempt(array(
            "nombre_usuario" => request()->input('nombre_usuario'),
            "password" => request()->input('password'),
            "usuario_activo" => true
        ))) {
            return response()
                ->json(
                    [
                        "status" => false,
                        "error" => 'Usuario o contraseña incorrectos'
                    ]
                );
        }

        return $this->responseWithToken($token);
    }

    private function responseWithToken($token)
    {
        $userProfile = DB::table('tab_perfiles')
            ->join('tab_usuario_perfiles', 'tab_perfiles.id_perfil', '=', 'tab_usuario_perfiles.id_perfil')
            ->join('tab_usuarios', 'tab_usuarios.id_usuario', '=', 'tab_usuario_perfiles.id_usuario')
            ->where('tab_usuarios.id_usuario', Auth::user()->id_usuario)
            ->limit(1)
            ->select('tab_perfiles.nombre_perfil')
            ->get();

        
        return response()
            ->json([
                "status" => true,
                "token" => $token,
                "expires_in" => auth()->factory()->getTTL()*60,
                'profile' => $userProfile[0]->nombre_perfil
            ]);
    }

    public function signout()
    {
        Auth::logout();

        return response()
            ->json(
                [
                    "status" => true
                ]
            );
    }
}
