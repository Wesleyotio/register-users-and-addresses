<?php
namespace App\Repositories;

use App\Models\Address;
use App\Models\User;
use App\Repositories\Interfaces\AddressRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class AddressRepositoryImpl implements AddressRepository {

    public function getFilterAddresses($userId, $filteredData)
    {

        $query = Address::where('user_id', $userId);
    
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

        return $query->get();
    }
    public function getAllAddresses($userId, $addressId)
    {

        return Address::where('user_id', $userId)->where('id', $addressId)->get();

    }

    public function createAddress(array $addressData)
    {
        $user = auth()->user();
        $user->addresses()->create($addressData);
        // Address::create($addressData);
    }

    public function updateAddress($userId, $addressId, $addressData)
    {
       
        Address::where('user_id', $userId )->where('id', $addressId)->update($addressData);
    }

    public function deleteAddress($userId, $addressId)
    {
      
        Address::where('user_id', $userId)->where('id', $addressId)->delete();
            
    }
}