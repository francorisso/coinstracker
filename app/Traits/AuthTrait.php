<?php namespace App\Traits;

use League\OAuth2\Client\Provider\CoinsTrackerProvider;

trait AuthTrait {

	/**
	 * Try to get an access token from the API
	 * 
	 * @return access_token(string)
	 */
	public function getAccessToken( $username="franco@720desarrollos.com", $password="franco123")
	{
		$provider = $this->getProviderInstance();

		//stub for GET call
		$params = [
	    	'grant_type'	 =>'password',
		    'client_id'      => $provider->clientId,
		    'client_secret'  => $provider->clientSecret,
		    'redirect_uri'   => '',
		    'username'	     => $username,
		    'password'		 => $password
	    ];
	    $tokenInfo = \Authorizer::issueAccessToken($params);

	    //$response = $this->call("POST", $provider->urlAccessToken(), $params);

	    //$tokenInfo = json_decode( $response->getContent() );
	    
	    return $tokenInfo['access_token'];
	}

	/**
	 * Returns a user id based on access_token
	 */
	public function getUserByAccessToken( $accessToken = null )
	{
		if(empty($accessToken)){
			$accessToken = \Request::input("access_token");
		}
		if(empty($accessToken)){
			abort(400, "Missing access token");
		}

		$user = \DB::table('oauth_sessions')
		->select('owner_id')
		->join("oauth_access_tokens", 'oauth_access_tokens.session_id', '=', 'oauth_sessions.id')
		->where('oauth_access_tokens.id', $accessToken)
		->where('oauth_sessions.owner_type', 'user')
		->first();
		
		$id = $user->owner_id;
		return $id;
	}

	/**
	* Create a provider instance
	*
	* @return $provider(CoinsTrackerProvider)
	*/
	public function getProviderInstance()
	{
		$provider = new CoinsTrackerProvider([
		    'clientId'      => "webapp",
		    'clientSecret'  => "CV9e8WbKT8Xe9T4FBi3RyF1J6eIJxwpt",
		]);

		return $provider;
	}

}
