<?php

namespace App\Http\Controllers;

use App\models\TabUser;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $users = DB::table('tab_usuarios')
            ->leftJoin('tab_usuario_perfiles', 'tab_usuarios.id_usuario', '=', 'tab_usuario_perfiles.id_usuario')
            ->leftJoin('tab_perfiles', 'tab_usuario_perfiles.id_perfil', '=', 'tab_perfiles.id_perfil')
            ->select(
                'tab_usuarios.id_usuario',
                'nombre_usuario',
                'primer_nombre',
                'primer_apellido',
                'usuario_activo',
                'tab_usuarios.mail_usuario',
                'tab_perfiles.nombre_perfil'
            )
            ->orderBy('nombre_usuario')
            ->get();

            if (!$users) {
                return response()->json(
                    [
                        "status" => false,
                        "error" => "No es posible obtener usuarios registrados..."
                    ]
                );
            }

            return response()->json(
                [
                    "status" => true,
                    "users" => $users
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


    public function verifyChangeUserPassword()
    {
        try {
            $response =  Auth::user() ? Auth::user()->cambiar_password : false;

            return response()
                ->json(
                    [
                        "status" => true,
                        "change_password" => $response
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

    public function getUserById(Request $request)
    {
        try {
            $user = DB::table('tab_usuarios')
                ->join('tab_usuario_perfiles', 'tab_usuarios.id_usuario', '=', 'tab_usuario_perfiles.id_usuario')
                ->join('tab_perfiles', 'tab_perfiles.id_perfil', '=', 'tab_usuario_perfiles.id_perfil')
                ->select(
                    "tab_usuarios.nombre_usuario",
                    "tab_usuarios.primer_nombre",
                    "tab_usuarios.mail_usuario",
                    "tab_usuarios.primer_apellido",
                    "tab_usuarios.usuario_activo",
                    "tab_perfiles.id_perfil",
                    "tab_perfiles.nombre_perfil"
                )
                ->where("tab_usuarios.id_usuario", $request->input('id_usuario'))
                ->get();

            if (!$user->count()) {
                return response()
                    ->json(
                        [
                            "status" => false,
                            "error" => "No es posible obtener usuario seleccionado..."
                        ]
                    );
            }

            return response()
                ->json(
                    [
                        "status" => true,
                        "user" => $user
                    ]
                );
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
        try {
            $validator = Validator::make($request->all(), [
                "nombre_usuario" => "required|string",
                "primer_nombre"=>"required|string",
                "primer_apellido" =>"required|string",
                "mail_usuario"=>"required|email",
                "password" => "required|min:8",
                "id_perfil"=>"required|integer"
            ]);
    
            if ($validator->errors()->count() > 0) {
                throw new Exception(
                    $validator
                        ->errors()
                        ->first()
                );
            }

            $user = DB::table('tab_usuarios')
                ->select('id_usuario')
                ->where('nombre_usuario', $request->input('nombre_usuario'))
                ->get();

            if ($user->count() > 0) {
                return response()
                    ->json(
                        [
                            "status"=>false,
                            "error" => "Nombre de usuario ya se encuentra registrado..."
                        ]
                    );
            }
            DB::transaction(function () use ($request) {
                $user = new TabUser();

                $user->nombre_usuario=$request->input('nombre_usuario');
                $user->primer_nombre = $request->input('primer_nombre');
                $user->primer_apellido =$request->input('primer_apellido');
                $user->mail_usuario=$request->input('mail_usuario');
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
                        "error" => "No es posible crear nuevo usuario..."
                    ]
                    );
                }
    
                $fromEmail = env('MAIL_FROM_ADDRESS', "f26deb3a11-a27e01@inbox.mailtrap.io");

                Mail::send('mails.resetPassword', ["tempPassword" => $request->input('password') ], function ($message) use ($request, $fromEmail) {
                    $message->to($request->input('mail_usuario'));
                    $message->from($fromEmail);
                    $message->subject('noreply');
                });
            });
            

            return response()->json(
                [
                    "status" => true,
                    "message" => "Usuario creado con exito..."
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
                        'usuario_activo' => $request->input('usuario_activo'),
                        'mail_usuario'=>$request->input('mail_usuario')
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
                    "message" => "Usuario actualizado con exito..."
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

    public function deactivateUser(Request $request)
    {
        try {
            $user = DB::table('tab_usuarios')
                ->where('id_usuario', $request->input('id_usuario'))
                ->update(
                    [
                        "usuario_activo" => false
                    ]
                );

            if (!$user) {
                return response()->json(
                    [
                            "status" => false,
                            "error" => "No es posible desactivar cuenta de usuario..."
                        ]
                );
            }

            return response()->json(
                [
                        "status" => true,
                        "message" => "Usuario desactivado con exito..."
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
