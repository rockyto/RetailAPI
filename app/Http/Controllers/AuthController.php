<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Models\User;
use \stdClass;
use Illuminate\Support\Facades\Hash;

use App\Models\Log;

class AuthController extends Controller
{

    public function login(Request $request)
    {

        if(empty($request->only('email', 'password'))) {



            return response()->json([
            'status' => "404",
            'message' => 'Se requieren ingresar datos de acceso'
            ], JSON_UNESCAPED_UNICODE);

            } else {

                if(!Auth::attempt($request->only('email', 'password')))
                {


                    return response()->json([
                        'status' => "401",
                        'message' => 'Datos de acceso incorrectos'
                    ], JSON_UNESCAPED_UNICODE);
                }else{

                    $user = User::where('email', $request['email'])->firstOrFail();

                    $token = $user->createToken('auth_token')->plainTextToken;



                    return response()->json([
                        'status' => "202",
                        'message' => "Inicio de sesión correcto",
                        'accessToken' => $token,
                        'email' => $user->email,
                        'name' => $user->name
                    ], JSON_UNESCAPED_UNICODE);
                }
        }
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'status' => "205",
            'message' => 'Logout exitoso'
            ], JSON_UNESCAPED_UNICODE);

    }

}
