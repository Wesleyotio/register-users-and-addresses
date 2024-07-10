<?php

namespace App\Repositories\Interfaces;

interface AuthRepository 
{
 
    public function register(string $name, string $cpf, string $email, string $phone, string $password);

}