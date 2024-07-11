<?php

namespace App\Repositories\Interfaces;



interface UserRepository 
{
 
    public function getUserData();
    public function updateUser(array $request);
    public function deleteUser();

}