<?php namespace App\Http\Controllers\Api\V1;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Expenses;

class ExpensesController extends Controller {
	use \App\Traits\AuthTrait;

	private $validFields = [
		"store" 	=> [
			"date",
			"hour",
			"minute",
			"description",
			"comment",
			"amount"
    	],
    	"show"		=> [
    		"id",
			"date",
			"hour",
			"minute",
			"description",
			"comment",
			"amount"	
    	],
    	"update"	=> [
    		"date",
			"hour",
			"minute",
			"description",
			"comment",
			"amount"
    	],
	];
	private $userId;

	public function __construct(){
		$this->userId = $this->getUserByAccessToken();
		if(empty( $this->userId )){
			abort(400, "Not valid access token");
		}
	}

	/**
	 * Returns expenses from a user.
	 * Node: GET /expenses
	 * 
	 * *Fields*
	 * offset  : (optional) From what position should I start getting records. Default: 0
	 * limit   : (optional) The number of expenses to return. Default: 20.
	 * orderBy : (optional) JSON with the order parameters as [{field:asc|desc},...]. Default: {date:desc}
	 * fields  : (optional) Comma separated list of fields to return from expenses.
	 * filters : (optional) Filters applied to the results in JSON format with comparator.
	 * 			 Support just for AND relationship btw filters for now.
	 * 			 Example: 	[{ 	
	 * 							date: {
	 * 								comparator: =,<,>
	 * 								value: yyyy/mm/dd
	 * 							} 
	 * 						}]
	 */
	public function index(Request $request)
	{
		//getting input values
		$offset 	= $request->input("offset", 0);
		$limit 		= $request->input("limit", 20);
		$orderBy	= $request->input("orderBy", json_encode(['date'=>'desc']));
		$fieldsRaw 	= $request->input("fields");
		$filtersRaw = $request->input("filters", null);

		// Offset
		$query = Expenses::skip( $offset )
			->take( $limit )
			->where( "user_id", $this->userId );

		$orderBy = json_decode( $orderBy, true );
		foreach($orderBy as $field=>$orderType){
			$query->orderBy($field, $orderType);
		}

		// Fields
		if(!empty( $fieldsRaw )){
			$fields = preg_split("/,/", $fieldsRaw);
			array_walk($fields, function( &$el, $key ){
				$el = trim($el);
			});

			// means that if there is a field not in the valid ones.
			$fieldsDiff = array_diff($fields, $this->validFields["show"]);
			if( !empty( $fieldsDiff ) ){
				abort(400, "Invalid Fields: ".implode(",",$fieldsDiff));
			}

			$query->select( $fields );
		} else {
			$query->select( $this->validFields["show"] );
		}

		// Filters
		if(!empty($filtersRaw)){
			$filters = json_decode( $filtersRaw, true );
			foreach( $filters as $field => $qInfo ){
				try {
					$comparator = ( empty($qInfo["comparator"])? "=" : $qInfo["comparator"] );
					$value = $qInfo["value"];
					$query->where( $field, $comparator, $value );
				} catch(\Exception $e){
					abort(400, "Bad request: check your filters parameters");
					return;
				}
			}
		}

		// Run query
		$expenses = $query->get();

		return response()->json( $expenses );
	}

	/**
	 * Creates a expense.
	 * Node: POST /expenses
	 * 
	 * *Fields*
	 * fields: (required) Fields for the request in JSON format 
	 * 
	 * @return expense_id(int)
	 */
	public function store(Request $request)
	{
		$fields = $request->input("fields");
		if(empty($fields)){
			abort(400, "Missed fields parameters");
		}
		$fields = json_decode( $fields, true );
		// means that if there is a field not in the valid ones.
		$fieldsDiff = array_diff(array_keys($fields), $this->validFields["store"]);
		if( !empty( $fieldsDiff ) ){
			abort(404, "Invalid Fields: ".implode(",",$fieldsDiff));
		}

		$expense = new Expenses;
		foreach($fields as $field => $value){
			$expense->{$field} = $value;
		}
		$expense->user_id = $this->userId;
		$expense->save();

		return response()->json([ "expense_id" => $expense->id ]);
	}

	/**
	 * Get a expense.
	 * Node: GET /expenses/{expense_id}
	 * 
	 * *Fields*
	 * fields: (required) Fields for the request in JSON format 
	 * 
	 * @return expense(Expense)
	 */
	public function show(Request $request, $id)
	{
		$fieldsRaw = $request->input("fields");
		$query = Expenses::where("id",'=',$id)
		->where('user_id',$this->userId);

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

		$expense = $query->first();
		if(empty($expense)){
			abort(404, "The Expense doesn't exists");
		}

		return response()->json( $expense );
	}

	/**
	 * Get a expense.
	 * Node: PUT /expenses/{expense_id}
	 * 
	 * *Fields*
	 * fields: (required) Fields for the request in JSON format 
	 * 
	 * @return expense(Expense)
	 */
	public function update($id)
	{
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

		$expense = Expenses::where("id", $id)
		->where("user_id", $this->userId);
		
		if( empty( $expense ) ){
			abort(404);
		}		
		foreach($fields as $field => $value){
			$expense->{$field} = $value;
		}
		$expense->save();

		return response()->json([ 'expense_id' => $expense->id ]);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$expense = Expenses::where("id", $id)
		->where("user_id", $this->userId);
		
		if( empty( $expense ) ){
			abort(404);
		}

		$expense->delete();
	}

}
