<?php

namespace App\Http\Controllers;

use App\Mail\ContactMail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function sendContactMessage(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "name" => "required|string",
                "phone" =>"required|string",
                "mail" => "required|email",
                "message" => "required|string"
                
            ]);

            if ($validator->errors()->count() > 0) {
                throw new Exception(
                    $validator
                        ->errors()
                        ->first()
                );
            }

            $contactMessage = new ContactMail(
                $request->input('message'),
                $request->input('mail'),
                $request->input('name'),
                $request->input('phone')
            );

            Mail::to("f26deb3a11-a27e01@inbox.mailtrap.io")->send($contactMessage);

            return response()
                ->json(
                    [
                        "status" => true,
                        "message" => "Mensaje enviado con éxito"
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
}
