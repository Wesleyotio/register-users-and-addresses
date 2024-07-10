<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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