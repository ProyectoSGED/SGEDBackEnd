<?php

namespace App\Http\Controllers;

use App\models\TabUser;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = new TabUser();

        try {
            $user->nombre_usuario=$request->input('nombre_usuario');
            $user->primer_nombre = $request->input('primer_nombre');
            $user->primer_apellido =$request->input('primer_apellido');
            $user->password = Hash::make($request->input('password'));
    
                
            if ($user->save()) {
                $userProfile =  DB::table('tab_usuario_perfiles')->insert(
                    [
                        'id_usuario' => $user->id_usuario,
                        'id_perfil' => $request->input('id_perfil')
                    ]
                );
            }
        
            if (!$user && !$userProfile) {
                return response()->json(
                    [
                        "status" => false,
                        "error" => "No es posible crear nuevo usuario"
                    ]
                );
            }
    
            return response()->json(
                [
                    "status" => true,
                    "message" => "Usuario crado con exito"
                ]
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    "status" => false,
                    "error" => $e->getMessage()
                ]
            );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            $user = DB::table('tab_usuarios')
                ->where('id_usuario', $request->input('id_usuario'))
                ->update(
                    [
                        'nombre_usuario' => $request->input('nombre_usuario'),
                        'primer_nombre' => $request->input('primer_nombre'),
                        'primer_apellido' => $request->input('primer_apellido'),
                        //SE DEBE CONFIRMAR SI SOLO ADMIN DEL SISTEMA PUEDE EDITAR CONTRASEÑAS
                        //'password' => Hash::make($request->input('password'));
                        'usuario_activo' => $request->input('usuario_activo')
                    ]
                );

            $userProfile = DB::table('tab_usuario_perfiles')
                ->where('id_usuario', $user)
                ->update(
                    [
                        'id_usuario' => $user,
                        'id_perfil' => $request->input('id_perfil')
                    ]
                );

            if (!$user && !$userProfile) {
                return response()->json(
                    [
                        "status" => false,
                        "error" => "No es posible actualizar usuario seleccionado"
                    ]
                );
            }

            return response()->json(
                [
                    "status" => true,
                    "message" => "Usuario actualizado con exito"
                ]
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    "status" => false,
                    "error" => $e->getMessage()
                ]
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
