<?php namespace App\Http\Controllers;

class AppController extends Controller {

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function index(\Request $request)
	{	
		return view('app');
	}
}
