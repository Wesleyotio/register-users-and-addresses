<?php

namespace App\Http\Controllers;

use App\Repositories\Interfaces\AuthRepository;
use Illuminate\Http\Request;

/**
 * 
 * 
 * @OA\Tag(
 *     name="Authenticate",
 *     description="Registration and user authentication"
 * )
 */
class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(private AuthRepository $authRepository)
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register user",
     *     tags={"Authenticate"},
     *     @OA\RequestBody(
     *         required=true,
    *             @OA\JsonContent(
    *             @OA\Property(property="name", type="string", example="João do Teste"),
    *             @OA\Property(property="email", type="string", example="João@teste.com"),
    *             @OA\Property(property="cpf", type="string", example="999.000.111-66"),
    *             @OA\Property(property="phone", type="string", example="(88) 99233-4490"),
    *             @OA\Property(property="password", type="string", example="87654321")
    *              )
    *     ),
    *     @OA\Response(
    *         response=201,
    *         description="Usuário criado com sucesso",
 *             @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="User successfully registered"),
 *               
 *         )
    *     )
    * )
    */
    public function register(Request $request) 
    {
      
        if ($this->isValidRegisterData($request)){

            try {

                $this->authRepository->register(
                    $request->input('name'),
                    $request->input('cpf'),
                    $request->input('email'),
                    $request->input('phone'),
                    $request->input('password')
                );
                
                return response()->json(['message' => 'User successfully registered'], 201,['Content-Type' =>'application/json']);
                
            } catch (\Throwable $th) {
                return response()->json(['message' => $th->getMessage()] , 500, ['Content-Type' =>'application/json']);
            }
        }


    }
    //
    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login user and get authentication token",
     *     tags={"Authenticate"},
     *     @OA\RequestBody(
     *         required=true,
    *             @OA\JsonContent(
    *             @OA\Property(property="email", type="string", example="email@test.com"),
    *             @OA\Property(property="password", type="string", example="12345678")
    *              )
    *     ),
    *     @OA\Response(
 *         response=200,
 *         description="Successful login",
 *         @OA\JsonContent(
 *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0L2FwaS9sb2dpbiIsImlhdCI6MTcyMDYyMTk2OCwiZXhwIjoxNzIwNjI1NTY4LCJuYmYiOjE3MjA2MjE5NjgsImp0aSI6Ilo3VU16TVhoek4wSXRqbXIiLCJzdWIiOiI0NiIsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.YfdDn3Ao5_2eq0JhsWTpP-jUCN9snUtBWQQHYbZ5Bik"),
 *             @OA\Property(property="token_type", type="string", example="bearer"),
 *             @OA\Property(property="expires_in", type="integer", example=3600)
 *         )
 *        ),
    *     @OA\Response(
    *         response=401,
    *         description="Invalid credentials",
    *         @OA\JsonContent(
    *             @OA\Property(property="error", type="string", example="Invalid credentials")
    *         )
    *     )
    * )
    */
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

      /**
     * @OA\Get(
     *     path="/api/me",
     *     summary="Shows the data of the logged-in user",
     *     tags={"Authenticate"},
     *     security={{"bearerAuth":{}}},
    *     @OA\Response(
    *         response=200,
    *         description="Usuário conectado com sucesso",
    *          @OA\JsonContent(
    *             @OA\Property(property="status", type="string", example="active"),
    *             @OA\Property(property="name", type="string", example="João do Teste"),
    *             @OA\Property(property="email", type="string", example="João@teste.com"),
    *             @OA\Property(property="cpf", type="string", example="999.000.111-66"),
    *             @OA\Property(property="phone", type="string", example="(88) 99233-4490")
    *              )
    *     )
    * )
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

       /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Shows the data of the logged-in user",
     *     tags={"Authenticate"},
     *     security={{"bearerAuth":{}}},
    *     @OA\Response(
    *         response=200,
    *         description="Usuário descontado com sucesso",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="Successfully logged out")
    *             
    *         )
    *     )
    * )
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