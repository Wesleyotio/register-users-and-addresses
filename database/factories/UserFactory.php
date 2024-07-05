<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $faker = \Faker\Factory::create('pt_BR');
        $faker->addProvider(new \Faker\Provider\pt_BR\Person($faker));
        $faker->addProvider(new \Faker\Provider\pt_BR\PhoneNumber($faker));
        $faker->addProvider(new \Faker\Provider\Internet($faker));


        return [
            'status' => rand(0,1) ? 'active':'inactive',
            'name' => $faker->name(),
            'email' => $faker->freeEmail(),
            'phone' => $faker->phoneNumber(),
            'cpf' => $faker->cpf(true),
            'password' => Hash::make('12345678')
        ];
    }
}
