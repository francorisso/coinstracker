<?php

use League\OAuth2\Client\Provider\CoinsTrackerProvider;

class AuthTest extends TestCase {

	/**
	* Create a provider instance
	*
	* @return $provider(CoinsTrackerProvider)
	*/
	public function testGetProviderInstance()
	{
		$provider = new CoinsTrackerProvider([
		    'clientId'      => "webapp",
		    'clientSecret'  => "CV9e8WbKT8Xe9T4FBi3RyF1J6eIJxwpt",
		]);

		return $provider;
	}

	/**
	 * Try to get an access token from the API
	 * @depends testGetProviderInstance
	 * @return access_token(string)
	 */
	public function testGetAccessToken( CoinsTrackerProvider $provider )
	{
		// If we don't have an authorization code then get one
	    $authUrl = $provider->urlAccessToken();
	    $response = $this->call('POST', $authUrl, [
	    	'grant_type'	 =>'password',
		    'client_id'      => $provider->clientId,
		    'client_secret'  => $provider->clientSecret,
		    'redirect_uri'   => '',
		    'scopes'         => [],
		    'username'		 => 'franco@720desarrollos.com',
		    'password'		 => 'franco123'
	    ]);
	    
	    $this->assertEquals(200, $response->getStatusCode());
	    
	    $tokenInfo = json_decode( $response->getContent() );

	    return $tokenInfo->access_token;
	}

	/**
	* Tries to do a request that requires valid access_token
	* Test both sides, with and without access token.
	*
	* @depends testGetAccessToken
	* @return void
	*/
	public function testProtectedRequest($access_token){
		
		//test if I can access without an access token
	    $response = $this->call("GET", "/api/v1/users" );
	    $this->assertEquals( 401, $response->getStatusCode() );

	    $response = $this->call("GET", "/api/v1/users", [
	    	"access_token" => $access_token
	    ]);
		$this->assertEquals( 200, $response->getStatusCode() );
	}

}
