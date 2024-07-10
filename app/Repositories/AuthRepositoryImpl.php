<?php
namespace App\Repositories;

use App\Repositories\Interfaces\AuthRepository;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class AuthRepositoryImpl implements AuthRepository {

    public function register(string $name, string $cpf, string $email,string $phone, string $password)
    {
        User::create([
            'status' => 'active',
            'name' => $name,
            'cpf' => $cpf,
            'email' => $email,
            'phone' => $phone,
            'password' => Hash::make($password)

        ]);  
    }
}