<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

 /**
 * @OA\Info(title="API Documentation", version="1.0.0")
 * @OA\Tag(
 *     name="Address",
 *     description="Operations on user addresses"
 * )
 */
class AddressController extends Controller
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
     *     path="/api/user/address/{id}",
     *     summary="Get list of addresses or a specific user",
     *     security={{"bearerAuth":{}}},
     *     tags={"Address"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the address",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="country",
     *         in="query",
     *         description="Country of the user",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="address",
     *         in="query",
     *         description="Address of the user",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="postcode",
     *         in="query",
     *         description="Postcode of the user",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="city",
     *         in="query",
     *         description="City of the user",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="stateAbbr",
     *         in="query",
     *         description="State abbreviation of the user",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of users or a specific user",
     *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="addresses",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=118),
 *                     @OA\Property(property="address", type="string", example="06262-340, R. Alcantara, 90125. Bloco C\nPietra do Norte - PR"),
 *                     @OA\Property(property="postcode", type="string", example="77999-726"),
 *                     @OA\Property(property="city", type="string", example="Quintana do Sul"),
 *                     @OA\Property(property="stateAbbr", type="string", example="SE"),
 *                     @OA\Property(property="country", type="string", example="Vietnã")
 *                 )
 *             )
 *         )
     *     )
     * )
     */
    public function index(Request $request) 
    {
        
        try {
            $user = auth()->user();

            $filteredForId = $request->only(['id']);

            if (!array_key_exists('id', $filteredForId) ) {

                $filteredData = $request->only([ 'address', 'postcode', 'city', 'stateAbbr', 'country']);
    
                $query = Address::where('user_id', $user->id);
    
                if (array_key_exists('address', $filteredData) ) {
                    $query = $query->where('address',$filteredData['address']);
                }
    
                if (array_key_exists('postcode', $filteredData) ) {
                    $query = $query->where('postcode',$filteredData['postcode']);
                }
    
                if (array_key_exists('city', $filteredData) ) {
                    $query = $query->where('city',$filteredData['city']);
                }
    
                if (array_key_exists('stateAbbr', $filteredData) ) {
                    $query = $query->where('stateAbbr',$filteredData['stateAbbr']);
                }
    
                if (array_key_exists('country', $filteredData) ) {
                    $query = $query->where('country',$filteredData['country']);
                }
    
                $addresses = $query->get();
                return response()->json(['addresses' => $addresses], 200);
            }

            $addresses = Address::where('user_id', $user->id)->where('id', $ $filteredForId['id'])->get();
            return response()->json(['addresses' => $addresses], 200);
        } catch (\Throwable $th) {
           
            return response()->json(['message' => $th->getMessage()], 500);
        }

    }
    /**
     * @OA\Post(
     *     path="/api/user/address/create",
     *     summary="Create address for user",
     *     security={{"bearerAuth":{}}},
     *     tags={"Address"},
     *     @OA\RequestBody(
     *         required=true,
    *             @OA\JsonContent(
    *             @OA\Property(property="address", type="string", example="Rua do Meio, 2525"),
    *             @OA\Property(property="postcode", type="string", example="62900-000"),
    *             @OA\Property(property="city", type="string", example="Ocara"),
    *             @OA\Property(property="stateAbbr", type="string", example="CE"),
    *             @OA\Property(property="country", type="string", example="Brazil")
    *              )
    *     ),
    *     @OA\Response(
    *         response=201,
    *         description="Endereço criado com sucesso",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="Created successfully address")
    *             
    *         )
    *     )
    * )
    */
    public function create(Request $request)
    {
        if ($this->isValidAddressData($request)) {

            try {
                $user = auth()->user();
                $filteredData = $request->only([ 'address', 'postcode', 'city', 'stateAbbr', 'country']);

                $user->addresses()->create($filteredData);

                return response()->json(['message' => 'Created successfully address'], 201);
            } catch (\Throwable $th) {
                return response()->json(['message' => $th->getMessage()], 500);
    
            }
        }
    }

    /**
     * @OA\Put(
     *     path="/api/user/address/update/{id}",
     *     summary="Update address for user",
     *     security={{"bearerAuth":{}}},
     *     tags={"Address"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the address",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
    *             @OA\JsonContent(
    *             @OA\Property(property="address", type="string", nullable=true, example="Rua do Meio, 2525"),
    *             @OA\Property(property="postcode", type="string", nullable=true, example="62900-000"),
    *             @OA\Property(property="city", type="string", nullable=true, example="Ocara"),
    *             @OA\Property(property="stateAbbr", type="string", nullable=true, example="CE"),
    *             @OA\Property(property="country", type="string", nullable=true, example="Brazil")
    *              )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Endereço atualizado com sucesso",
    *          @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="Updated successfully address")
    *             
    *         )
    *     )
    * )
    */
    public function update(Request $request)
    {
        if ($this->isValidUpdateAddressData($request)) {

            try {
              
                $user = auth()->user();
            
                $filteredData = $request->only([ 'address', 'postcode', 'city', 'stateAbbr', 'country']);

                Address::where('user_id', $user->id)
                    ->where('id', $request->id)
                    ->update($filteredData);

                return response()->json(['message' => 'Updated successfully address'], 200);
                
               
            } catch (\Throwable $th) {
                return response()->json(['message' => $th->getMessage()], 500);
            }
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/user/address/update/{id}",
     *     summary="Delete address for user",
     *     security={{"bearerAuth":{}}},
     *     tags={"Address"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the address",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *
    *     @OA\Response(
    *         response=200,
    *         description="Endereço removido com sucesso",
    *          @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="Updated successfully address")
    *             
    *         )
    *     ),
    *     @OA\Response(
    *         response=401,
    *         description="Usuário não autorizado",
    *          @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="Unauthorized")
    *             
    *         )
    *     )
    * )
    */
    public function delete(Request $request)
    {
        if ($this->isValidDeleteAddress($request)) {

            try {
                
                $user = auth()->user();
                if (Hash::check($request->password, $user->password)) {
                    
                    Address::where('user_id', $user->id)
                        ->where('id', $request->id)
                        ->delete();
                    return response()->json(['message' => 'successfully deleted address']);
                }
                return response()->json(['message' => 'Unauthorized'], 401);
            } catch (\Throwable $th) {
                //throw $th;
                return response()->json(['message' => $th->getMessage()], 500);
            }
        }
    }
    
    protected function isValidAddressData(Request $request): array 
    {
        
        return $this->validate($request,[
            'address'   => 'string|max:255',
            'postcode'  => 'string|max:255',
            'city'      => 'string|max:255',
            'stateAbbr' => 'string|size:2',
            'country'   => 'string|max:30'

        ]);
    }
    protected function isValidUpdateAddressData(Request $request): array 
    {
        
        return $this->validate($request,[
            'address'   => 'string|max:255',
            'postcode'  => 'string|max:255',
            'city'      => 'string|max:255',
            'stateAbbr' => 'string|size:2',
            'country'   => 'string|max:50',
            
        ]);
    }

    protected function isValidDeleteAddress(Request $request): array 
    {
        
        return $this->validate($request,[
            'password'  =>  'required|string',
        ]);
    }

    
    
}