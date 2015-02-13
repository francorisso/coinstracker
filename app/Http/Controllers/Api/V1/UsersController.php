<?php namespace App\Http\Controllers\Api\V1;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\User;

use Illuminate\Http\Request;

class UsersController extends Controller {
	use \App\Traits\AuthTrait;

	private $validFields = [
		"show" => [ "name", "email" ],
		"update" => [ "name", "email", "password" ]
	];

	/**
	 * Returns a JSON with user information
	 * Node: GET /users/{user_id}
	 * 
	 * *Fields*
	 * fields: 	(optional) Separated comma list of user's information to return. If empty returns all the fields
	 * 
	 */
	public function show( Request $request, $id )
	{
		$fieldsRaw = $request->input("fields");

		$query = User::where( "id", $id );
		
		if(!empty( $fieldsRaw )){
			$fields = preg_split("/,/", $fieldsRaw);
			array_walk($fields, function( &$el, $key ){
				$el = trim($el);
			});

			// means that if there is a field not in the valid ones.
			$fieldsDiff = array_diff($fields, $this->validFields["show"]);
			if( !empty( $fieldsDiff ) ){
				abort(404, "Invalid Fields: ".implode(",",$fieldsDiff));
			}

			$query->select( $fields );
		} else {
			$query->select( $this->validFields["show"] );
		}
		
		$user = $query->first();
		if(empty($user)){
			abort(404, "User not found");
		}

		return response()->json( $user );
	}

	/**
	 * Update fields of user information
	 * Node: PUT /users/{user_id}
	 * 
	 * *Fields*
	 * fields: 	JSON with pairs (field, value) for update.
	 * 
	 */
	public function update(Request $request, $id)
	{
		//only allow same user here
		$user_id = $this->getUserByAccessToken();
		if( $user_id != $id ){
			return response()->json("User not requested", 401);
		}

		$fields = $request->input("fields");
		if(empty($fields)){
			abort(400, "Missed fields parameters");
		}
		$fields = json_decode( $fields, true );
		// means that if there is a field not in the valid ones.
		$fieldsDiff = array_diff(array_keys($fields), $this->validFields["update"]);
		if( !empty( $fieldsDiff ) ){
			abort(400, "Invalid Fields: ".implode(",",$fieldsDiff));
		}

		$user = User::find( $id );
		foreach($fields as $field => $value){
			$user->{$field} = $value;
		}
		$user->save();

		return response()->json( $user );
	}

	/**
	 * Get access token
	 */
	public function accessToken( Request $request ){
		$username = $request->input("email");
		$password = $request->input("password");
		
		return response()->json( $this->getAccessToken( $username, $password) );
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}
