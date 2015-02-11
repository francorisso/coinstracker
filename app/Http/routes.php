<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'WelcomeController@index');

Route::get('home', 'HomeController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

//API
Route::group(
	[
		'before' => 'oauth', 
		'prefix' => 'api/v1'
	], 
	function(){
		// Expenses controller
		Route::resource('expenses', 'Api\V1\ExpensesController');

		// Users controller
		Route::resource('users', 'Api\V1\UsersController');
	}
);

Route::post('oauth/access_token', function() {
    return Response::json(Authorizer::issueAccessToken());
});