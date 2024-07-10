<?php

namespace Tests;

use App\Models\Address;
use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;


class AddressTest extends TestCase
{
    

    protected function setUp() : void 
    {
        parent::setUp();

        $faker = \Faker\Factory::create('pt_BR');
        $faker->addProvider(new \Faker\Provider\pt_BR\Person($faker));
        $faker->addProvider(new \Faker\Provider\pt_BR\PhoneNumber($faker));
        $faker->addProvider(new \Faker\Provider\Internet($faker));
        $faker->addProvider(new \Faker\Provider\pt_BR\Address($faker));
    }

    
    public function test_get_address()
    {
        
        $user = User::factory()->has(Address::factory()->count(3))->create();
        $address = Address::where('user_id', $user->id)->first();

        $response = $this->call('POST', 'api/login', [
            'email' => $user->email,
            'password' => '12345678',
        ], [],[], [
            "Content-Type"=>"application/json" 
        ]);
        
        $this->assertEquals(200, $response->status());

        $responseLogin = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('token', $responseLogin);
        $this->assertNotEmpty($responseLogin['token']);

        $token = $responseLogin['token'];

        $responseAddress = $this->call('GET', 'api/user/address', [
            'stateAbbr' => $address->stateAbbr
        ], [],[], [
            "Authorization" => "Bearer ". $token,
            "Content-Type"=>"application/json" 
        ]);
      
        $responseAddressJson = json_decode($responseAddress->getContent(), true);
        
        $this->assertArrayHasKey('stateAbbr', $responseAddressJson['addresses'][0]);
        $this->assertEquals($address->stateAbbr, $responseAddressJson['addresses'][0]['stateAbbr']);
        $this->assertEquals(200, $responseAddress->status());

              
    }

    public function test_get_address_for_id()
    {
        
        $user = User::factory()->has(Address::factory()->count(3))->create();
        $address = Address::where('user_id', $user->id)->first();

        $response = $this->call('POST', 'api/login', [
            'email' => $user->email,
            'password' => '12345678',
        ], [],[], [
            "Content-Type"=>"application/json" 
        ]);
        
        $this->assertEquals(200, $response->status());

        $responseLogin = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('token', $responseLogin);
        $this->assertNotEmpty($responseLogin['token']);

        $token = $responseLogin['token'];

        $url = 'api/user/address/'. $address->id;

        $responseAddress = $this->call('GET', $url, [], [],[], [
            "Authorization" => "Bearer ". $token,
            "Content-Type"=>"application/json" 
        ]);

        $responseAddressJson = json_decode($responseAddress->getContent(), true);
    
        $this->assertEquals($address->id, $responseAddressJson['addresses'][0]['id']);
        $this->assertEquals(200, $responseAddress->status());
    }

    public function test_address_data_created()
    {
        
        $user = User::factory()->create();

        $response = $this->call('POST', 'api/login', [
            'email' => $user->email,
            'password' => '12345678',
        ], [],[], [
            "Content-Type"=>"application/json" 
        ]);
        
        $this->assertEquals(200, $response->status());

        $responseLogin = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('token', $responseLogin);
        $this->assertNotEmpty($responseLogin['token']);

        $token = $responseLogin['token'];


        $responseUser = $this->call('POST', 'api/user/address/create/', [
            'address'   =>  'Rua do Meio, 2525',
            'postcode'  =>  '62900-000',
            'city'      =>  'russas',
            'stateAbbr' =>  'CE',
            'country'   =>  'BR'
        ], [],[], [
            "Authorization" => "Bearer ". $token,
            "Content-Type"=>"application/json" 
        ]);
        $this->assertEquals(201, $responseUser->status());
              
    }

    public function test_address_data_updated()
    {
        
        $user = User::factory()->has(Address::factory()->count(2))->create();
        $address = Address::where('user_id', $user->id)->first();

        $response = $this->call('POST', 'api/login', [
            'email' => $user->email,
            'password' => '12345678',
        ], [],[], [
            "Content-Type"=>"application/json" 
        ]);
        
        $this->assertEquals(200, $response->status());

        $responseLogin = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('token', $responseLogin);
        $this->assertNotEmpty($responseLogin['token']);

        $token = $responseLogin['token'];

        $url = 'api/user/address/update/'. $address->id;

        $responseAddress = $this->call('PUT', $url, [
            'address'   =>  'Rua do Meio, 2525',
            'postcode'  =>  '62900-000',
            'city'      =>  'russas',
            'stateAbbr' =>  'CE',
            'country'   =>  'BR'
        ], [],[], [
            "Authorization" => "Bearer ". $token,
            "Content-Type"=>"application/json" 
        ]);
        
        $this->assertEquals(200, $responseAddress->status());
              
    }

    
    public function test_address_data_deleted()
    {
        $user = User::factory()->has(Address::factory()->count(3))->create();
        $address = Address::where('user_id',$user->id)->first();
        $response = $this->call('POST', 'api/login', [
            'email' => $user->email,
            'password' => '12345678',
        ], [],[], [
            "Content-Type"=>"application/json" 
        ]);
        
        $this->assertEquals(200, $response->status());

        $responseLogin = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('token', $responseLogin);
        $this->assertNotEmpty($responseLogin['token']);

        $token = $responseLogin['token'];

        $url = 'api/user/address/delete/' . $address->id;

        $responseUser = $this->call('DELETE', $url, [
            'password'      => '12345678',
        ], [],[], [
            "Authorization" => "Bearer ". $token,
            "Content-Type"=>"application/json" 
        ]);
        
        $this->assertEquals(200, $responseUser->status());
              
    }
  
    
}
