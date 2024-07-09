<?php

namespace Tests;

use App\Models\User;
use Tests\TestCase;


class AuthTest extends TestCase
{
    


    protected $userData;
    protected $incompleteUserData;

    
    protected function setUp() : void 
    {
        parent::setUp();

        $faker = \Faker\Factory::create('pt_BR');
        $faker->addProvider(new \Faker\Provider\pt_BR\Person($faker));
        $faker->addProvider(new \Faker\Provider\pt_BR\PhoneNumber($faker));
        $faker->addProvider(new \Faker\Provider\Internet($faker));
        $faker->addProvider(new \Faker\Provider\pt_BR\Address($faker));

      
        $this->userData =  [
            'name'          => $faker->name(),
            'email'         => $faker->freeEmail(),
            'phone'         => $faker->phoneNumber(),
            'cpf'           => $faker->cpf(true),
            'password'      => '12345678'
            
        ];
        $this->incompleteUserData =  [
            'name'          => $faker->name(),
            'email'         => $faker->freeEmail(),
            'password'      => '12345678'
            
        ];
    }

    public function test_when_we_have_invalidated_required_fields()
    {
        $response = $this->call('POST','api/register', $this->incompleteUserData, [
            "Accept"=>"application/json",   
        ]);
        
        $this->assertEquals(422, $response->status());
        
    }
    
    public function test_when_user_has_successfully_registered()
    {
        
        $response = $this->call('POST', 'api/register', $this->userData, [],[], [
            "Content-Type"=>"application/json" 
        ]);
        $this->assertEquals(201, $response->status());
     

    }
    public function test_when_the_user_has_successfully_logged_in()
    {
        
        $user = User::factory()->create();

        $response = $this->call('POST', 'api/login', [
            'email' => $user->email,
            'password' => '12345678',
        ], [],[], [
            "Content-Type"=>"application/json" 
        ]);
        $this->assertEquals(200, $response->status());
     
    }
    
    public function test_to_see_logged_in_user()
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

        $responseMe = $this->call('GET', 'api/me', [], [],[], [
            "Authorization" => "Bearer ". $token,
            "Content-Type"=>"application/json" 
        ]);
      
        $this->assertEquals(200, $responseMe->status());

        $responseData = json_decode($responseMe->getContent(), true);

        $this->assertArrayHasKey('id', $responseData);
        $this->assertEquals($user->id, $responseData['id']);
        
    }

    public function test_user_logout()
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

        $responseLogout = $this->call('POST', 'api/logout', [], [],[], [
            "Authorization" => "Bearer ". $token,
            "Content-Type"=>"application/json" 
        ]);
      
        $this->assertEquals(200, $responseLogout->status());

        $responseData = json_decode($responseLogout->getContent(), true);

        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('Successfully logged out',$responseData['message']);
        
    }

    public function test_user_refresh()
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

        $responseRefresh = $this->call('GET', 'api/refresh', [], [],[], [
            "Authorization" => "Bearer ". $token,
            "Content-Type"=>"application/json" 
        ]);
      
        $this->assertEquals(200, $responseRefresh->status());

        $responseData = json_decode($responseRefresh->getContent(), true);

        $this->assertArrayHasKey('token', $responseData);
        $this->assertNotEmpty($responseData['token']);
        
    }
    
}
