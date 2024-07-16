<?php

namespace App\Repositories\Interfaces;



interface AddressRepository 
{
 
    public function getFilterAddresses($userId, $filterData);
    public function getAllAddresses($userId, $addressId);
    public function createAddress(array $addressData);
    public function updateAddress($userId, $addressId, array $addressData);
    public function deleteAddress($userId, $addressId);

}