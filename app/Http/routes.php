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

Blade::setEscapedContentTags('[[', ']]');
Blade::setContentTags('[[[', ']]]');

//For ajax requests
Route::group(
	[
		'prefix' => 'ajax'
	], 
	function(){
		Route::get('/', function(){
		});

		Route::controllers([
			'auth' => 'Auth\AuthController',
			'password' => 'Auth\PasswordController',
		]);

		Route::post("authorize", "Api\V1\UsersController@accessToken");
	}
);

//API
Route::post('api/v1/users/create', 'Api\V1\UsersController@store');

Route::group(
	[
		'before' => 'oauth', 
		'prefix' => 'api/v1'
	], 
	function(){
		
		Route::get('/validToken', function(){
			response()->json( json_encode(["message"=>"not valid"]), 400);
		});

		// Expenses controller
		Route::get('expenses/weekly', 'Api\V1\ExpensesController@weekly');
		Route::post('expenses/delete/{id}', 'Api\V1\ExpensesController@destroy');
		Route::resource('expenses', 'Api\V1\ExpensesController');

		// Users controller
		Route::resource('users', 'Api\V1\UsersController');
	}
);

Route::post('oauth/access_token', function() {
    return Response::json(Authorizer::issueAccessToken());
});

Route::get('/', 'AppController@index');

Route::get('/{page}', 'AppController@index')
->where('page','[a-zA-Z0-9]+');

Route::get('/{page}/{subpage}', 'AppController@index')
->where('page','[a-zA-Z0-9]+')
->where('subpage','[a-zA-Z0-9]+');

Route::get('/{page}/{subpage}/{subsub}', 'AppController@index')
->where('page','[a-zA-Z0-9]+')
->where('subpage','[a-zA-Z0-9]+')
->where('subsub','[a-zA-Z0-9]+');