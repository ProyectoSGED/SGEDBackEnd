<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        $this
            ->middleware(
                'auth:api',
                [
                    'except'=>[
                        'signin',
                        'sendResetPasswordEmail',
                        'changePassword'
                    ]
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

    public function changePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "current_password" => "required|min:8",
                "password" => "required_with:password_confirmation|min:8|confirmed",
                "password_confirmation"=>"required|min:8",
            ]);
    
            if ($validator->errors()->count() > 0) {
                throw new Exception(
                    $validator
                        ->errors()
                        ->first()
                );
            }

            $checkCurrentPassword = Hash::check($request->input('current_password'), Auth::user()->password);

            if (!$checkCurrentPassword) {
                throw new Exception("Contraseña actual no es correcta...");
            }

            $newPassword =  DB::table('tab_usuarios')
                ->where('tab_usuarios.id_usuario', Auth::user()->id_usuario)
                ->update(
                    [
                        'tab_usuarios.password' => Hash::make($request->input('new_password')),
                        'cambiar_password' => false
                    ]
                );

            if (!$newPassword) {
                throw new Exception("Ocurrio un error al crear nueva contraseña...");
            }

            return response()
                ->json(
                    [
                        "status" => true,
                        "message" => "Contraseña actualizada con éxito..."
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

    public function sendResetPasswordEmail(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "user_email" => "required|email",
            ]);

            if ($validator->errors()->count() > 0) {
                throw new Exception(
                    $validator
                        ->errors()
                        ->first()
                );
            }

            $user = DB::table('tab_usuarios')
                ->where('tab_usuarios.mail_usuario', $request->input('user_email'))
                ->get();
            
            if (!$user->count()) {
                throw new Exception("Email de usuario no se encuentra registrado...");
            }

            $tempPassword = $this->randomPassword();

            $newPassword = DB::table('tab_usuarios')
                ->where('id_usuario', $user[0]->id_usuario)
                ->update(
                    [
                        'password' => Hash::make($tempPassword),
                        'cambiar_password' => true
                    ]
                );

            if (!$newPassword) {
                throw new Exception("Ocurrio un error al crear nueva contraseña...");
            }

            $fromEmail = env('MAIL_FROM_ADDRESS', "f26deb3a11-a27e01@inbox.mailtrap.io");

            Mail::send('mails.resetPassword', ["tempPassword" => $tempPassword ], function ($message) use ($request, $fromEmail) {
                $message->to($request->input('user_email'));
                $message->from($fromEmail);
                $message->subject('noreply');
            });


            return response()
                ->json(
                    [
                        "status" => true,
                        "message" => "Nueva contraseña enviada con éxito"
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

    private function randomPassword()
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!-.[]?*()';

        $newPassword = "";

        $charsList = mb_strlen($chars, '8bit')-1;
        
        foreach (range(1, 8) as $i) {
            $newPassword .= $chars[random_int(0, $charsList)];
        }

        return $newPassword;
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
