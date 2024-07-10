<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * @OA\Info(title="API Documentation", version="1.0.0")
 */

 /**
 * @OA\Tag(
 *     name="User",
 *     description="Operations about users"
 * )
 */
class UserController extends Controller
{

     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    /**
     * @OA\Get(
     *     path="/api/user",
     *     summary="List of user data and addresses",
     *     security={{"bearerAuth":{}}},
     *     tags={"User"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista o usuário conectado e seus endereços",
     *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="user",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=46),
 *                 @OA\Property(property="status", type="string", example="inactive"),
 *                 @OA\Property(property="name", type="string", example="Dr. Adriele Daniele Bonilha"),
 *                 @OA\Property(property="email", type="string", example="josefina.dacruz@yahoo.com"),
 *                 @OA\Property(property="cpf", type="string", example="190.176.342-00"),
 *                 @OA\Property(property="phone", type="string", example="(68) 96398-7768"),
 *                 @OA\Property(
 *                     property="addresses",
 *                     type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="id", type="integer", example=117),
 *                         @OA\Property(property="address", type="string", example="37059-597, R. Aurora, 226. Bc. 35 Ap. 12\nPorto Regiane do Sul - MG"),
 *                         @OA\Property(property="postcode", type="string", example="63600-159"),
 *                         @OA\Property(property="city", type="string", example="São Benedito"),
 *                         @OA\Property(property="stateAbbr", type="string", example="ES"),
 *                         @OA\Property(property="country", type="string", example="Vanuatu")
 *                     )
 *                 )
 *             )
 *         )
*     )
     * )
     */
    public function index() 
    {
        
        try {
            $user = auth()->user();
            $addresses = $user->addresses;
            return response()->json(['user' => $user, 'addresses' => $addresses], 200);
        } catch (\Throwable $th) {
           
            return response()->json(['message' => $th->getMessage()], 500);
        }

    }
    /**
     * @OA\Put(
     *     path="/api/user/update",
     *     summary="Update logged in user",
     *     security={{"bearerAuth":{}}},
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
    *             @OA\JsonContent(
    *             @OA\Property(property="name", type="string", nullable=true, example="Jão do Teste"),
    *             @OA\Property(property="email", type="string", nullable=true, example="jao@email.com"),
    *             @OA\Property(property="phone", type="string", nullable=true, example="(99) 99234-9876"),
    *             @OA\Property(property="cpf", type="string", nullable=true, example="999.111.000-09"),
    *             @OA\Property(property="password", type="string", example="10987654321"),
    *             @OA\Property(property="new_password", type="string", nullable=true, example="12345678")
    *              )
        *     ),
        *     @OA\Response(
        *         response=200,
        *         description="Atualiza dados do usuário conectado",
    *             @OA\JsonContent(
    *                @OA\Property(property="message", type="string", example="Updated successfully")
    *             )
        *     )
        * )
        */
    public function update(Request $request)
    {
        if ($this->isValidUpdateData($request)) {

            try {
              
                $user = auth()->user();
                if (Hash::check($request->password, $user->password)) {

                    $filteredDataPassword = $request->only(['new_password']);
                    if( array_key_exists('new_password', $filteredDataPassword) ) {
                        
                        $filteredData = $request->only(['name', 'email','cpf', 'phone']); 
                        $filteredData['password'] = Hash::make($filteredDataPassword['new_password']); 

                        User::where('id', $user->id)->update($filteredData);

                        return response()->json(['message' => 'updated data and password'], 200);
                    }
                    
                    $filteredData = $request->only(['name', 'email','cpf', 'phone']);
                    User::where('id', $user->id)->update($filteredData);
                    return response()->json(['message' => 'Updated successfully'], 200);
                }
                return response()->json(['message' => 'Unauthorized'], 401);
            } catch (\Throwable $th) {
                return response()->json(['message' => $th->getMessage()], 500);
            }
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/user/delete",
     *     summary="Delete and logout user",
     *     security={{"bearerAuth":{}}},
     *     tags={"User"},
     *     @OA\Response(
     *         response=200,
     *         description="Remove usuário conectado assim como todos os endereços associados a ele",
     *         @OA\JsonContent(
    *                @OA\Property(property="message", type="string", example="successfully deleted user")
    *          )
     *     )
     * )
     */
    public function delete(Request $request)
    {
        if ($this->isValidDeleteUser($request)) {

            try {
                
                $user = auth()->user();
                if (Hash::check($request->password, $user->password)) {
                    
                    auth()->logout();
                    User::where('id', $user->id)->delete();
                    return response()->json(['message' => 'successfully deleted user']);
                }
                return response()->json(['message' => 'Unauthorized'], 401);
            } catch (\Throwable $th) {
                //throw $th;
                return response()->json(['message' => $th->getMessage()], 500);
            }
        }
    }
    
    protected function isValidUpdateData(Request $request): array 
    {
        
        return $this->validate($request,[
            'name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:users,email',
            'phone' => 'string|max:16',
            'cpf'   => 'string|size:14|unique:users,cpf',
            'password' => 'required|string',
            'new_password' => 'string'
        ]);
    }

    protected function isValidDeleteUser(Request $request): array 
    {
        
        return $this->validate($request,[
            'password' => 'required|string',
        ]);
    }

    
    
}