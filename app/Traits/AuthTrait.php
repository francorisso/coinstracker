<?php namespace App\Traits;

use League\OAuth2\Client\Provider\CoinsTrackerProvider;

trait AuthTrait {

	/**
	 * Try to get an access token from the API
	 * 
	 * @return access_token(string)
	 */
	public function getAccessToken()
	{
		$provider = $this->getProviderInstance();

		//stub for GET call
		$params = [
	    	'grant_type'	 =>'client_credentials',
		    'client_id'      => $provider->clientId,
		    'client_secret'  => $provider->clientSecret,
		    'redirect_uri'   => '',
	    ];
	    $response = $this->call("POST", $provider->urlAccessToken(), $params);

	    $tokenInfo = json_decode( $response->getContent() );
	    
	    return $tokenInfo->access_token;
	}

	/**
	* Create a provider instance
	*
	* @return $provider(CoinsTrackerProvider)
	*/
	private function getProviderInstance()
	{
		$provider = new CoinsTrackerProvider([
		    'clientId'      => "webapp",
		    'clientSecret'  => "CV9e8WbKT8Xe9T4FBi3RyF1J6eIJxwpt",
		]);

		return $provider;
	}

}
