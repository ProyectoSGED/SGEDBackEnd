<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
        try {
            $validator = Validator::make($request->all(), [
                "nombre_usuario" => "required|string",
                "password" => "required|min:8"
            ]);
    
            if ($validator->errors()->count() > 0) {
                throw new Exception(
                    $validator
                        ->errors()
                        ->first()
                );
            }
    
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
        } catch (Exception $e) {
            return response()
                ->json(
                    [
                        "status" => false,
                        "error" => $e->getMessage()
                    ]
                );
        }
    }

    private function responseWithToken($token)
    {
        $userProfile = DB::table('tab_perfiles')
            ->join('tab_usuario_perfiles', 'tab_perfiles.id_perfil', '=', 'tab_usuario_perfiles.id_perfil')
            ->join('tab_usuarios', 'tab_usuarios.id_usuario', '=', 'tab_usuario_perfiles.id_usuario')
            ->where('tab_usuarios.id_usuario', Auth::user()->id_usuario)
            ->limit(1)
            ->select(
                'tab_perfiles.nombre_perfil',
                "tab_usuarios.primer_nombre",
                "tab_usuarios.primer_apellido"
            )
            ->get();

        
        return response()
            ->json([
                "status" => true,
                "token" => $token,
                "access_type" => 'bearer',
                "expires_in" => auth()->factory()->getTTL()*1,
                'profile' => strtoupper($userProfile[0]->nombre_perfil),
                "user" => $userProfile[0]->primer_nombre." ".$userProfile[0]->primer_apellido
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
