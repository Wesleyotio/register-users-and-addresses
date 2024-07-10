<?php

namespace Tests;

use App\Models\Address;
use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;


class UserTest extends TestCase
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

    
    public function test_get_user_data_and_their_address()
    {
        
        $user = User::factory()->has(Address::factory()->count(3))->create();
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

        $responseUser = $this->call('GET', 'api/user', [], [],[], [
            "Authorization" => "Bearer ". $token,
            "Content-Type"=>"application/json" 
        ]);
      
        $this->assertEquals(200, $responseUser->status());
              
    }

    public function test_user_data_updated()
    {
        
        $user = User::factory()->has(Address::factory()->count(3))->create();
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

        $responseUser = $this->call('PUT', 'api/user/update', [
            'phone'         => '(41) 4837-2949',
            'password'      => '12345678'
        ], [],[], [
            "Authorization" => "Bearer ". $token,
            "Content-Type"=>"application/json" 
        ]);
        $this->assertEquals(200, $responseUser->status());
              
    }

    public function test_user_data_not_updated()
    {
        
        $user = User::factory()->has(Address::factory()->count(3))->create();
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

        $responseUser = $this->call('PUT', 'api/user/update', [
            'phone'         => '(88) 4837-2949',
            'password'      => '1234567'
        ], [],[], [
            "Authorization" => "Bearer ". $token,
            "Content-Type"=>"application/json" 
        ]);
        
        $this->assertEquals(401, $responseUser->status());
              
    }

    public function test_invalid_user_data()
    {
        
        $user = User::factory()->has(Address::factory()->count(3))->create();
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

        $responseUser = $this->call('PUT', 'api/user/update', [
            'phone'         => '(88) 4837-2949',
            'new_password'  => '12345678'
        ], [],[], [
            "Authorization" => "Bearer ". $token,
            "Content-Type"=>"application/json" 
        ]);
        $this->assertEquals(422, $responseUser->status());
              
    }

    public function test_user_data_updated_password()
    {
        
        $user = User::factory()->has(Address::factory()->count(3))->create();
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

        $responseUser = $this->call('PUT', 'api/user/update', [
            'phone'         => '(98) 9999-9999',
            'password'      => '12345678',
            'new_password'  => '123456789'
        ], [],[], [
            "Authorization" => "Bearer ". $token,
            "Content-Type"=>"application/json" 
        ]);
        $this->assertEquals(200, $responseUser->status());
              
    }
    public function test_user_data_deleted()
    {
        $user = User::factory()->has(Address::factory()->count(3))->create();
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

        $responseUser = $this->call('DELETE', 'api/user/delete', [
            'password'      => '12345678',
           
        ], [],[], [
            "Authorization" => "Bearer ". $token,
            "Content-Type"=>"application/json" 
        ]);
        
        $this->assertEquals(200, $responseUser->status());
              
    }
  
    
}
