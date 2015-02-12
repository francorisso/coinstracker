<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expenses extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'expenses';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [ 
		"user_id",
		"date",
		"hour",
		"minute",
		"description",
		"comment",
		"amount"
    ];
}
