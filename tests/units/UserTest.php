<?php

namespace Tests\Units;


use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use stdClass;

class UserTest extends TestCase
{
    

    

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
    
    protected function setUp() : void 
    {
        
    }

    
    #[DataProvider('userDataProvider')]
    public function test_user_created_successfully($userData, $value, $message)
    {
        
        $mockQueryBuilder = Mockery::mock('alias:'. DB::class);

        $mockQueryBuilder->shouldReceive('table')
                        ->with('users')
                        ->andReturnSelf();

        $mockQueryBuilder->shouldReceive('insert')
                        ->once()
                        ->with($userData)
                        ->andReturnUsing(function() use ($userData) {
                            return ($userData['status'] == 'active' || $userData['status'] == 'inactive') ? true :false;
                        } );

        $response = DB::table('users')->insert($userData);

        // $this->assertEquals($response, $value, $message);
        $this->assertEquals($value, $response, $message);
    }

    #[DataProvider('userDataReadProvider')]
    public function test_user_read_successfully($userData, $value)
    {
        
        $mockQueryBuilder = Mockery::mock('alias:' . DB::class);
  
        $mockQueryBuilder->shouldReceive('table')
            ->with('users')
            ->andReturnSelf();
       
        $mockQueryBuilder->shouldReceive('where')
            ->with('id', 1)
            ->andReturnSelf();

        $mockQueryBuilder->shouldReceive('first')
            ->andReturnUsing(function() use ($userData) {
                return ($userData['id'] == 1) ? (object) $userData : null;
            } );

        
        $result = DB::table('users')->where('id', 1)->first();

        $this->assertEquals($value, is_null($result));
  
    }

    #[DataProvider('userDataUpdatedProvider')]
    public function test_user_updated_successfully($userData, $value)
    {
        
        $mockQueryBuilder = Mockery::mock('alias:' . DB::class);
  
        $mockQueryBuilder->shouldReceive('table')
            ->with('users')
            ->andReturnSelf();
       
        $mockQueryBuilder->shouldReceive('where')
            ->with('id', 1)
            ->andReturnSelf();

        $mockQueryBuilder->shouldReceive('update')
            ->with($userData)
            ->andReturnUsing(function() use ($userData) {
                return ($userData['status'] == 'active') ? 1 : 0;
            } );
        
        $result = DB::table('users')->where('id', 1)->update($userData);
        $this->assertEquals($value, $result);
    }

    #[DataProvider('userDataDeletedProvider')]
    public function test_user_deleted_successfully($userData, $value)
    {
        
        $mockQueryBuilder = Mockery::mock('alias:' . DB::class);
  
        $mockQueryBuilder->shouldReceive('table')
            ->with('users')
            ->andReturnSelf();
       
        $mockQueryBuilder->shouldReceive('where')
            ->with('id', 1)
            ->andReturnSelf();

        $mockQueryBuilder->shouldReceive('delete')
            ->with($userData)
            ->andReturnUsing(function() use ($userData) {
                return ($userData['status'] == 'inactive') ? 1 : 0;
            } );
        
        $result = DB::table('users')->where('id', 1)->delete($userData);

        $this->assertEquals($value, $result);

    }

    public  static function userDataProvider(): array 
    {
        $faker = \Faker\Factory::create('pt_BR');
        $faker->addProvider(new \Faker\Provider\pt_BR\Person($faker));
        $faker->addProvider(new \Faker\Provider\pt_BR\PhoneNumber($faker));

        $userDataActive =  [
            'status'    => 'active',
            'name'      => $faker->name(),
            'email'     => $faker->freeEmail(),
            'phone'     => $faker->phoneNumber(),
            'cpf'       => $faker->cpf(true),
            'password'  => '$2y$12$Teng30Fdli4j/VAAxW5kUu5I1E3t1UqCe81mm3XIFsYozyxAbhBDG', 
        ];
        $userDataWrong =  [
            'status'    => '123',
            'name'      => $faker->name(),
            'email'     => $faker->freeEmail(),
            'phone'     => $faker->phoneNumber(),
            'cpf'       => $faker->cpf(true),
            'password'  => '$2y$12$Teng30Fdli4j/VAAxW5kUu5I1E3t1UqCe81mm3XIFsYozyxAbhBDG', 
        ];
        return  [
            'when_the_data_is_correct' => ['userData' => $userDataActive, 'value' => true, 'message' => 'registered user' ], 
            'when_the_data_is_NOT_correct' => ['userData' => $userDataWrong, 'value' => false, 'message' => 'user not registered' ] 
        ];

    }

    public  static function userDataReadProvider(): array 
    {
        $faker = \Faker\Factory::create('pt_BR');
        $faker->addProvider(new \Faker\Provider\pt_BR\Person($faker));
        $faker->addProvider(new \Faker\Provider\pt_BR\PhoneNumber($faker));

        $userDataActive =  [
            'id'        =>  1,
            'status'    => 'active',
            'name'      => $faker->name(),
            'email'     => $faker->freeEmail(),
            'phone'     => $faker->phoneNumber(),
            'cpf'       => $faker->cpf(true),
            'password'  => '$2y$12$Teng30Fdli4j/VAAxW5kUu5I1E3t1UqCe81mm3XIFsYozyxAbhBDG', 
        ];
        
        $userDataInactive =  [
            'id'        =>  2,
            'status'    => 'inactive',
            'name'      => $faker->name(),
            'email'     => $faker->freeEmail(),
            'phone'     => $faker->phoneNumber(),
            'cpf'       => $faker->cpf(true),
            'password'  => '$2y$12$Teng30Fdli4j/VAAxW5kUu5I1E3t1UqCe81mm3XIFsYozyxAbhBDG', 
        ];
        return  [
            'when_the_user_was_found' => ['userData' => $userDataActive, 'value' => false ], 
            'when_the_user_was_NOT_found' => ['userData' => $userDataInactive, 'value' => true ] 
        ];

    } 

    public  static function userDataUpdatedProvider(): array 
    {
        $faker = \Faker\Factory::create('pt_BR');
        $faker->addProvider(new \Faker\Provider\pt_BR\Person($faker));
        $faker->addProvider(new \Faker\Provider\pt_BR\PhoneNumber($faker));

        $userDataActive =  [
            'status'    => 'active',
            'name'      => $faker->name(),
            'phone'     => $faker->phoneNumber(), 
        ];
        
        $userDataInactive =  [
            'status'    => 'inactive',
            'name'      => $faker->name(),
            'phone'     => $faker->phoneNumber(),
        ];
        return  [
            'when_the_user_updates_the_data' => ['userData' => $userDataActive, 'value' => 1 ], 
            'when_the_user_NOT_updates_the_data' => ['userData' => $userDataInactive, 'value' => 0 ] 
        ];

    }

    public  static function userDataDeletedProvider(): array 
    {
        $faker = \Faker\Factory::create('pt_BR');
        $faker->addProvider(new \Faker\Provider\pt_BR\Person($faker));
        $faker->addProvider(new \Faker\Provider\pt_BR\PhoneNumber($faker));

        $userDataActive =  [
            'status'    => 'active',
            'name'      => $faker->name(),
            'phone'     => $faker->phoneNumber(), 
        ];
        
        $userDataInactive =  [
            'status'    => 'inactive',
            'name'      => $faker->name(),
            'phone'     => $faker->phoneNumber(),
        ];
        
        return  [
            'when_the_user_was_deleted' => ['userData' => $userDataActive, 'value' => 0 ], 
            'when_the_user_NOT_was_deleted' => ['userData' => $userDataInactive, 'value' => 1 ] 
        ];

    } 
    
}
