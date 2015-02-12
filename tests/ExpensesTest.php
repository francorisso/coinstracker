<?php

use League\OAuth2\Client\Provider\CoinsTrackerProvider;
use App\Models\User;
use App\Models\Expenses;

class ExpensesTest extends TestCase {

	use App\Traits\AuthTrait;

	private $authToken;
	private $testUserId = 1;
	
	/*
	 * Needed for Laravel to use dataProviders
	 */
	public function __construct($name = null, array $data = array(), $dataName = '') {
        parent::__construct($name, $data, $dataName);

        $this->createApplication();
    }

	/**
	 * Get from users the top expenses.
	 * 
	 */
	public function testGetFromUser(){
		$user_id = $this->testUserId;

		$path = "/api/v1/expenses";
		
		$authToken = $this->getAccessToken();
		$this->assertFalse( empty($authToken) );
		
		$expensesIds = Expenses::where("user_id", $user_id)
			->take(20)
			->orderBy('date', 'desc')
			->lists('id');
		if(empty($expensesIds)){
			return;
		}

		//set route parameters
		$params = [];
		$params["fields"] = "id";
		$params["user_id"] = $user_id;
		$params["orderBy"] = json_encode(['date'=>'desc']);
		//nonono...
		$response = $this->call("GET", $path, $params);
		$this->assertEquals(400, $response->getStatusCode());
		//cool,cool,cool
		$params["access_token"] = $authToken;
		$response = $this->call("GET", $path, $params);
		$this->assertEquals(200, $response->getStatusCode()
			, "Path " . $path 
		);
		
		$expensesNew = json_decode( $response->getContent() );
		$expensesNewIds = [];
		foreach($expensesNew as $expenseNew){
			$expensesNewIds[] = $expenseNew->id;
		}

		$diff = array_diff( $expensesNewIds, $expensesIds );
		$this->assertEmpty( $diff );

		//test wrong fields
		$paramsNew = $params;
		$paramsNew["fields"] = "id,notValidField";
		$response = $this->call("GET", $path, $paramsNew);
		$this->assertEquals(400, $response->getStatusCode());

		//test limits
		$paramsNew = $params;
		$paramsNew["offset"] = 10;
		$paramsNew["limit"]  = 10;
		$response = $this->call("GET", $path, $paramsNew);
		$this->assertEquals(200, $response->getStatusCode());
		
		$expensesNew = json_decode( $response->getContent() );
		$expensesNewIds = [];
		foreach($expensesNew as $expenseNew){
			$expensesNewIds[] = $expenseNew->id;
		}
		$this->assertLessThan(11, count($expensesNewIds));
		$this->assertEquals(
			$expensesNewIds, 
			array_slice($expensesIds, $paramsNew["offset"], $paramsNew["limit"])
		);

		$expensesNew = json_decode( $response->getContent() );
	}


	/**
	 * Get from users expenses with filters applied
	 * 
	 */
	public function testGetFromUserWithFilters(){
		$user_id = $this->testUserId;
		$path = "/api/v1/expenses";
		
		$authToken = $this->getAccessToken();
		$this->assertFalse( empty($authToken) );
		
		$filters = [
			"date" => [
				"comparator" 	=> "=",
				"value"			=> date("Y-m-d")
			],
			"description" => [
				"comparator"	=> "LIKE",
				"value"			=> "This is a description%"
			]
		];

		$expensesIds = Expenses::where("user_id", $user_id)
			->take(20)
			->orderBy('date', 'desc')
			->where(
				'date',
				$filters["date"]["comparator"],
				$filters["date"]["value"]
			)
			->where(
				'description',
				$filters["description"]["comparator"],
				$filters["description"]["value"]
			)
			->lists('id');
		
		//set route parameters
		$params = [];
		$params["fields"] = "id";
		$params["orderBy"] = json_encode(['date'=>'desc']);
		$params["filters"] = json_encode($filters);
		//nonono...
		$response = $this->call("GET", $path, $params);
		$this->assertEquals(400, $response->getStatusCode());
		//cool,cool,cool
		$params["access_token"] = $authToken;
		$response = $this->call("GET", $path, $params);
		$this->assertEquals(200, $response->getStatusCode()
			, "Path " . $path 
		);	

		$expensesNew = json_decode( $response->getContent() );
		$expensesNewIds = [];
		foreach($expensesNew as $expense){
			$expensesNewIds[] = $expense->id;
		}
		$this->assertEquals( $expensesIds, $expensesNewIds );
	}

	/**
     * Data Provider: it gives a list of random expenses
     * 
     * @return array(expenses)
     */
    public function testExpensesProvider(){
    	$user_id = $this->testUserId;
    	$expenses = Expenses::limit(10)->select(
    		'id',
    		'description',
    		'date',
    		'hour',
    		'minute',
    		'amount',
    		'comment'
    	)
    	->where('user_id',$user_id)
    	->get()->toArray();
    	$tests = [];
    	foreach($expenses as $expense){
    		$tests[] = [
    			$expense
    		];
    	}

    	return $tests;
    }

	/**
	 * Get one expense by ID
	 * 
	 * @dataProvider testExpensesProvider
	 */
	public function testGetExpense( $expense ){
		$fields = ['id',
    		'description',
    		'date',
    		'hour',
    		'minute',
    		'amount',
    		'comment'
    	];
		$path = "/api/v1/expenses/" . $expense['id'];
		
		$authToken = $this->getAccessToken();
		$this->assertFalse( empty($authToken) );

		//nop...
		$params = [];
		$response = $this->call("GET", $path, $params);
		$this->assertEquals(400, $response->getStatusCode());

		//sounds fair
		$params["access_token"] = $authToken;
		$params["fields"] = implode(",", $fields);
		$response = $this->call("GET", $path, $params);
		$this->assertEquals( 200, $response->getStatusCode());
		
		$expenseNew = json_decode( $response->getContent(), true );
		$this->assertEquals($expense, $expenseNew);
	}

	/**
	 * Update an expense
	 * 
	 * @dataProvider testExpensesProvider
	 */
	public function testUpdateExpense( $expense ){
		$expenseCopy = $expense;
		$expenseCopy["description"] .= "<---testing";
		unset($expenseCopy["id"]);

		$authToken = $this->getAccessToken();
		$this->assertFalse( empty($authToken) );
		
		//set route parameters
		$path = "/api/v1/expenses";
		$params = [];
		$params["fields"] = json_encode( $expenseCopy );
		//nonono...
		$response = $this->call("POST", $path, $params);
		$this->assertEquals(400, $response->getStatusCode());
		//cool,cool,cool
		$params["access_token"] = $authToken;
		$response = $this->call("POST", $path, $params);
		$this->assertEquals(200, $response->getStatusCode());

		$expense_id = json_decode( $response->getContent() );
		$expenseNew = Expenses::find( $expense_id->expense_id );
		$this->assertInstanceOf( "App\Models\Expenses", $expenseNew );

		foreach($expenseCopy as $field=>$value){
			$this->assertEquals( $value, $expenseNew->{$field} );
		}
	}

	/**
	 * Delete an expense
	 * 
	 * @dataProvider testExpensesProvider
	 */
	public function testDeleteExpense($expense){
		$authToken = $this->getAccessToken();
		$this->assertFalse( empty($authToken) );
		
		//set route parameters
		$path = "/api/v1/expenses/".$expense["id"];
		$params = [];
		//nonono...
		$response = $this->call("DELETE", $path, $params);
		$this->assertEquals(400, $response->getStatusCode());
		//cool,cool,cool
		$params["access_token"] = $authToken;
		$response = $this->call("DELETE", $path, $params);
		$this->assertEquals(200, $response->getStatusCode());
		
		//the resource should not exists anymore
		$response = $this->call("GET", $path, $params);
		$this->assertEquals(404, $response->getStatusCode());
	}

	/**
     * Data Provider: it gives a few new expenses for a set of users
     * 
     * @return array(expenses)
     */
    public function testFakeExpensesProvider(){
    	
    	$expenses = [];
    	for($i=0; $i<10; $i++){
    		$expenses[] = [[
    			"date" 			=> date("Y-m-d"),
    			"hour" 			=> rand(0, 23),
    			"minute"		=> rand(0, 59),
    			"description" 	=> "This is a description #".$i,
    			"comment"		=> "This is a comment #".$i,
    			"amount"		=> rand(0,100).".".rand(10,99)
    		]];
    	}
    	return $expenses;
    }

	/**
	 * Test the addition
	 * 
	 * @dataProvider testFakeExpensesProvider
	 */
	public function testAdd( $expense ){
		$authToken = $this->getAccessToken();
		$this->assertFalse( empty($authToken) );
		
		//set route parameters
		$path = "/api/v1/expenses";
		$params = [];
		$params["fields"] = json_encode( $expense );
		//nonono...
		$response = $this->call("POST", $path, $params);
		$this->assertEquals(400, $response->getStatusCode());
		//cool,cool,cool
		$params["access_token"] = $authToken;
		$response = $this->call("POST", $path, $params);
		$this->assertEquals(200, $response->getStatusCode());

		$expense_id = json_decode( $response->getContent() );
		$expenseNew = Expenses::find( $expense_id->expense_id );
		$this->assertInstanceOf( "App\Models\Expenses", $expenseNew );

		foreach($expense as $field=>$value){
			$this->assertEquals( $value, $expenseNew->{$field} );
		}

	}
}