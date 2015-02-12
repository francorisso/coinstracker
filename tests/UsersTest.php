<?php

use League\OAuth2\Client\Provider\CoinsTrackerProvider;
use App\Models\User;

class UsersTest extends TestCase {

	use App\Traits\AuthTrait;

	/*
	 * Needed for Laravel to use dataProviders
	 */
	public function __construct($name = null, array $data = array(), $dataName = '') {
        parent::__construct($name, $data, $dataName);

        $this->createApplication();
    }

	/**
	 * Provides an array of pairs (name, email) of users
	 * @return users(array)
	 */
	public function usersProvider(){
		$users = User::select("id", "name", "email")->take(10)->get();
		$values = [];
		foreach($users as $user){
			$values[] = [
				$user->id,
				[
					"name"   => $user->name,
					"email"  => $user->email
				]
			];
		}

		return $values;
	}
	
	/**
	 * Get user's information
	 * 
	 * @dataProvider usersProvider
	 */
	public function testGetProfile( $user_id, $user ){
		//don't do business here sir.
		$response = $this->call("GET", "/api/v1/users/".$user_id,[
			"fields" => "email, name"
		]);
		$this->assertEquals(400, $response->getStatusCode());

		//oh, you have an access, allow me to deliver the info sir.
		$access_token = $this->getAccessToken();
		$this->assertFalse( empty($access_token) );
		$response = $this->call("GET", "/api/v1/users/".$user_id,[
			"access_token" => $access_token,
			"fields" => "email, name"
		]);
		$this->assertEquals(
			200, $response->getStatusCode(), 
			"Calling: /api/v1/users/".$user_id." accessToken:".$access_token
		);

		$userNew = json_decode( $response->getContent(), true );
		$this->assertEquals( $user, $userNew );
	}

	/**
	 * Provides fake users for create them
	 */
	public function fakeUsersProvider(){
		
		$values = [];
		for($i=0;$i<10;$i++){
			$values[] = [[
				'name'=>'Franco '.$i,
				'email'=>'franco'.$i."@fake.com",
				'password'=>'franco123',
			]];
		}
		return $values;
	}

	/**
	* Creates a user in the system
	* 
	* @dataProvider fakeUsersProvider
	* @return user( User )
	*/
	public function testCreateUser( $user ){
		User::where( "email", $user["email"] )->delete();
		$userNew = User::where( "email", $user["email"] )->first();
		$this->assertEmpty( $userNew );

		$this->call("post", "/auth/register",array_merge([
			"_token" => csrf_token(),
			"password_confirmation" => "franco123"
		], $user));
		
		$userNew = User::where( "email", $user["email"] )->first();

		$this->assertInstanceOf( "App\Models\User", $userNew );
		$this->assertEquals( $user["name"], $userNew->name );
		$this->assertEquals( $user["email"], $userNew->email );		
		
		$userNew->password = $user["password"];

		return $userNew;
	}

	/**
	 * Try to update user's info
	 * 
	 * @dataProvider fakeUsersProvider
	 */
	public function testUpdateUserFailure( $user ){
		$userOld = User::where( "email", $user["email"] )->first();
		$this->assertFalse( empty($userOld) );

		//set route parameters
		$nameNew = $userOld->name . "_extra";
		$path = "/api/v1/users/".$userOld->id;
		$params = [
			"fields" => json_encode([
				"name" => $nameNew
			])
		];
		
		//don't do business here sir.
		$response = $this->call("PUT", $path, $params);
		$this->assertEquals(400, $response->getStatusCode());

		//access token only valid for user_id = 1
		$params["access_token"] = $this->getAccessToken();
		$this->assertFalse( empty($params["access_token"]) );

		$response = $this->call("PUT", $path, $params);
		$this->assertEquals(
			401, $response->getStatusCode(), 
			"Calling: " . $path . " accessToken:".$params['access_token']
		);
	}

	/**
	 * Try to update user's info
	 * 
	 * @dataProvider fakeUsersProvider
	 */
	public function testUpdateUser( $user ){
		$userOld = User::where( "email", $user["email"] )->first();
		$this->assertFalse( empty($userOld) );

		//set route parameters
		$nameNew = $userOld->name . "_extra";
		$path = "/api/v1/users/".$userOld->id;
		$params = [
			"fields" => json_encode([
				"name" => $nameNew
			])
		];
		
		//don't do business here sir.
		$response = $this->call("PUT", $path, $params);
		$this->assertEquals(400, $response->getStatusCode());
		
		//now it logins and then goes the update
		$params["access_token"] = $this->getAccessToken($user["email"],$user["password"]);
		$this->assertFalse( empty($params["access_token"]) );
		$response = $this->call("PUT", $path, $params);
		$this->assertEquals(
			200, $response->getStatusCode(), 
			"Calling: " . $path . "\n".$response->getContent()
		);

		$userNew = json_decode( $response->getContent() );
		$this->assertEquals( $nameNew, $userNew->name );
	}


	/**
	 * Logins a user in the system
	 * 
	 * @dataProvider fakeUsersProvider
	 */
	public function testLoginUser( $user ){
		$userReal = User::where( "email", $user["email"] )->first();
		$this->assertFalse( empty($userReal) );
		
		$accessToken = $this->getAccessToken( $userReal->email, $user["password"]);
		$this->assertFalse( empty($accessToken) );
		$this->assertEquals( $userReal->id, $this->getUserByAccessToken( $accessToken ) );
	}

}