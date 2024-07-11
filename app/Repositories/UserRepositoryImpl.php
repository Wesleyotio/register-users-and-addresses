<?php
namespace App\Repositories;


use App\Models\User;
use App\Repositories\Interfaces\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class UserRepositoryImpl implements UserRepository {

    public function getUserData()
    {

        return auth()->user();

    }

    public function updateUser($arrayData)
    {
        $user = auth()->user();
        User::where('id', $user->id)->update($arrayData);
    }

    public function deleteUser()
    {
        $user = auth()->user();
        User::where('id', $user->id)->delete();
            
    }
}