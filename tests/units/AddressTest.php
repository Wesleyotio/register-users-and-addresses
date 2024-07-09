<?php

namespace Tests\Units;


use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;


class AddressTest extends TestCase
{
    


    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
    
    protected function setUp() : void 
    {

    }
    
    #[DataProvider('addressDataProvider')]
    public function test_address_created_successfully($addressData, $value, $message)
    {
        
        $mockAddressQueryBuilder = Mockery::mock('alias:'. DB::class);

        $mockAddressQueryBuilder->shouldReceive('table')
                        ->with('addresses')
                        ->andReturnSelf();

        $mockAddressQueryBuilder->shouldReceive('insert')
                        ->once()
                        ->with($addressData)
                        ->andReturnUsing(function() use ($addressData) {
                            return ($addressData['stateAbbr'] !== 'ABC' ) ? true : false;
                        } );

        $response = DB::table('addresses')->insert($addressData);
               
        $this->assertEquals($value, $response, $message);
    }

    #[DataProvider('addressDataReadProvider')]
    public function test_address_read_successfully($addressData, $value)
    {
        
        $mockAddressQueryBuilder = Mockery::mock('alias:' . DB::class);
  
        $mockAddressQueryBuilder->shouldReceive('table')
            ->with('addresses')
            ->andReturnSelf();
       
        $mockAddressQueryBuilder->shouldReceive('where')
            ->with('user_id', 1)
            ->andReturnSelf();

        $mockAddressQueryBuilder->shouldReceive('first')
            ->andReturnUsing(function() use ($addressData) {
                return ($addressData['user_id'] == 1) ? (object) $addressData : null;
            } );

        
        $result = DB::table('addresses')->where('user_id', 1)->first();

        $this->assertEquals($value, is_null($result));
  
    }

    #[DataProvider('addressDataUpdatedProvider')]
    public function test_address_updated_successfully($addressData, $value)
    {
        
        $mockAddressQueryBuilder = Mockery::mock('alias:' . DB::class);
  
        $mockAddressQueryBuilder->shouldReceive('table')
            ->with('addresses')
            ->andReturnSelf();
       
        $mockAddressQueryBuilder->shouldReceive('where')
            ->with('user_id', 1)
            ->andReturnSelf();

        $mockAddressQueryBuilder->shouldReceive('update')
            ->with($addressData)
            ->andReturnUsing(function() use ($addressData) {
                return ($addressData['user_id'] == 1) ? 1 : 0;
            } );
        
        $result = DB::table('addresses')->where('user_id', 1)->update($addressData);
        $this->assertEquals($value, $result);
    }

    #[DataProvider('addressDataDeletedProvider')]
    public function test_address_deleted_successfully($addressData, $value)
    {
        
        $mockAddressQueryBuilder = Mockery::mock('alias:' . DB::class);
  
        $mockAddressQueryBuilder->shouldReceive('table')
            ->with('addresses')
            ->andReturnSelf();
       
        $mockAddressQueryBuilder->shouldReceive('where')
            ->with('user_id', 1)
            ->andReturnSelf();

        $mockAddressQueryBuilder->shouldReceive('delete')
            ->with($addressData)
            ->andReturnUsing(function() use ($addressData) {
                return ($addressData['user_id'] == 1) ? 1 : 0;
            } );
        
        $result = DB::table('addresses')->where('user_id', 1)->delete($addressData);
        $this->assertEquals($value, $result);

    }

    public  static function addressDataProvider(): array 
    {
        $faker = \Faker\Factory::create('pt_BR');
        $faker->addProvider(new \Faker\Provider\pt_BR\Address($faker));

        $addressData =  [
            'user_id'       => 1,
            'address'       => $faker->address(),
            'postcode'      => $faker->postcode(),
            'city'          => $faker->city(),
            'stateAbbr'     => $faker->stateAbbr(),
            'country'       => $faker->country(),
        ];
        $addressDataWrong =  [
            'user_id'       => 2,
            'address'       => $faker->address(),
            'postcode'      => $faker->postcode(),
            'city'          => $faker->city(),
            'stateAbbr'     => 'ABC',
            'country'       => $faker->country()
        ];
        return  [
            'when_the_data_is_correct' => ['addressData' => $addressData, 'value' => true, 'message' => 'registered address' ], 
            'when_the_data_is_NOT_correct' => ['addressData' => $addressDataWrong, 'value' => false, 'message' => 'address not registered' ] 
        ];

    }

    public  static function addressDataReadProvider(): array 
    {
        $faker = \Faker\Factory::create('pt_BR');
        $faker->addProvider(new \Faker\Provider\pt_BR\Address($faker));

        $addressData =  [
            'user_id'       => 1,
            'address'       => $faker->address(),
            'postcode'      => $faker->postcode(),
            'city'          => $faker->city(),
            'stateAbbr'     => $faker->stateAbbr(),
            'country'       => $faker->country(),
        ];
        $addressDataOtherUser =  [
            'user_id'       => 2,
            'address'       => $faker->address(),
            'postcode'      => $faker->postcode(),
            'city'          => $faker->city(),
            'stateAbbr'     => $faker->stateAbbr(),
            'country'       => $faker->country()
        ];
        return  [
            'when_the_address_was_found' => ['addressData' => $addressData, 'value' => false ], 
            'when_the_address_was_NOT_found' => ['addressData' => $addressDataOtherUser, 'value' => true ] 
        ];

    } 

    public  static function addressDataUpdatedProvider(): array 
    {
        $faker = \Faker\Factory::create('pt_BR');
        $faker->addProvider(new \Faker\Provider\pt_BR\Address($faker));

        $addressData =  [
            'user_id'       => 1,
            'address'       => $faker->address(),
            'postcode'      => $faker->postcode(),
            'city'          => $faker->city(),
            'stateAbbr'     => $faker->stateAbbr(),
            'country'       => $faker->country(),
        ];
        $addressDataOtherUser =  [
            'user_id'       => 2,
            'address'       => $faker->address(),
            'postcode'      => $faker->postcode(),
            'city'          => $faker->city(),
            'stateAbbr'     => $faker->stateAbbr(),
            'country'       => $faker->country()
        ];
        return  [
            'when_the_address_updates_the_data' => ['addressData' => $addressData, 'value' => 1 ], 
            'when_the_address_NOT_updates_the_data' => ['addressData' => $addressDataOtherUser, 'value' => 0 ] 
        ];

    }

    public  static function addressDataDeletedProvider(): array 
    {
        $faker = \Faker\Factory::create('pt_BR');
        $faker->addProvider(new \Faker\Provider\pt_BR\Address($faker));

        $addressData =  [
            'user_id'       => 1,
            'address'       => $faker->address(),
            'postcode'      => $faker->postcode(),
            'city'          => $faker->city(),
            'stateAbbr'     => $faker->stateAbbr(),
            'country'       => $faker->country(),
        ];
        $addressDataOtherUser =  [
            'user_id'       => 2,
            'address'       => $faker->address(),
            'postcode'      => $faker->postcode(),
            'city'          => $faker->city(),
            'stateAbbr'     => $faker->stateAbbr(),
            'country'       => $faker->country()
        ];
        
        return  [
            'when_the_address_was_deleted' => ['addressData' => $addressData, 'value' => 1], 
            'when_the_address_NOT_was_deleted' => ['addressData' => $addressDataOtherUser, 'value' => 0 ] 
        ];

    } 
    
}
