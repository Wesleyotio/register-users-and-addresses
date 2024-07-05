<?php

namespace Database\Factories;

use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Address::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $faker =  \Faker\Factory::create('pt_BR');
        $faker->addProvider(new \Faker\Provider\pt_BR\Address($faker));
        return [
            'address'       => $faker->address(),
            'postcode'      => $faker->postcode(),
            'city'          => $faker->city(),
            'stateAbbr'     => $faker->stateAbbr(),
            'country'       => $faker->country()
            
        ];
    }
}
