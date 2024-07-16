<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Repositories\Interfaces\AddressRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

    private AddressRepository $addressRepository;

    public function __construct(AddressRepository $addressRepository)
    {
        $this->addressRepository = $addressRepository;
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
    
                $addresses = $this->addressRepository->getFilterAddresses($user->id, $filteredData);

                return response()->json(['addresses' => $addresses]);
            }

           
            $addresses = $this->addressRepository->getAllAddresses($user->id, $filteredForId['id']);
        
            return response()->json(['addresses' => $addresses]);
        } catch (\Throwable $th) {
           
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
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
               
                $filteredData = $request->only([ 'address', 'postcode', 'city', 'stateAbbr', 'country']);

                $this->addressRepository->createAddress($filteredData);

                return response()->json(['message' => 'Created successfully address'], Response::HTTP_CREATED);
            } catch (\Throwable $th) {
                return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
    
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

                $this->addressRepository->updateAddress($user->id, $request->id, $filteredData);
                
                return response()->json(['message' => 'Updated successfully address']);
                
               
            } catch (\Throwable $th) {
                return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
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
                    
                    $this->addressRepository->deleteAddress($user->id, $request->id);
                    
                    return response()->json(['message' => 'successfully deleted address']);
                }
                return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
            } catch (\Throwable $th) {
                //throw $th;
                return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
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