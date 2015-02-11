<?php

use League\OAuth2\Client\Provider\CoinsTrackerProvider;
use App\Models\User;

class UsersTest extends TestCase {

	public function atestCreateUser(){
		$user = [
			"name" => "Franco Risso",
			"email" => "franco@720desarrollos.com",
			"password" => "franco123",
		];

		$this->call("post", "/auth/register",array_merge([
			"_token" => csrf_token(),
			"password_confirmation" => "franco123"
		], $user));
		
		$newUser = User::where( "email", $user["email"] )->first();

		$this->assertFalse( empty($newUser) );

		$this->assertEquals( $user, [
			"name" => $newUser->name,
			"email" => $newUser->email
		]);		
	}

}