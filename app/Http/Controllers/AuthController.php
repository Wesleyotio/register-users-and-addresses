<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Hashing\BcryptHasher;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    public function register(Request $request) 
    {
      
        if ($this->isValidRegisterData($request)){

            try {
        
                User::create([
                    'status' => 'active',
                    'name' => $request->input('name'),
                    'cpf' => $request->input('cpf'),
                    'email' => $request->input('email'),
                    'phone' => $request->input('phone'),
                    'password' => Hash::make($request->input('password'))
    
                ]);  
                return response()->json(['message' => 'User successfully registered'], 201,['Content-Type' =>'application/json']);
                
            } catch (\Throwable $th) {
                return response()->json(['message' => $th->getMessage()] , 500, ['Content-Type' =>'application/json']);
            }
        }


    }
    //
    public function login(Request $request)
    {
        if ($this->isValidLoginData($request)) {

            try {
                $credentials = [
                    'email' => $request->input('email'),
                    'password' => $request->input('password'),
                ];
    
                
                if (! $token = auth()->attempt($credentials)) {
                    return response()->json(['error' => 'Unauthorized. Invalid credentials or user not found.'], 401, ['Content-Type' =>'application/json']);
                }
        
             
                return $this->respondWithToken($token);
            } catch (\Throwable $th) {
                return response()->json(['message' => $th->getMessage()] , 500, ['Content-Type' =>'application/json']);
            }
        }
    }
     /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }
    
    protected function isValidRegisterData(Request $request): array 
    {
        
        return $this->validate($request,[
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'required|string|max:16',
            'cpf'   => 'required|string|size:14',
            'password' => 'required|string'
            
        ]);
    }

    protected function isValidLoginData(Request $request): array 
    {
        
        return $this->validate($request,[
           
            'email' => 'required|string|email|max:255',   
            'password' => 'required|string'
            
        ]);
    }
}