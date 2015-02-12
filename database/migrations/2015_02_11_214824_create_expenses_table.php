<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpensesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::dropIfExists('expenses');

		Schema::create('expenses', function($table)
		{
		    $table->increments('id');
		    $table->integer('user_id')
		    ->unsigned();
		    $table->foreign('user_id')
		    ->references('id')
		    ->on('users')
		    ->onDelete('cascade')
		    ->onUpdate('cascade');
		    $table->date("date")->index();
		    $table->integer("hour");
		    $table->integer("minute");
		    $table->text("description");
		    $table->decimal("amount",11,2);
		    $table->text("comment");
		    $table->timestamps();

		    $table->index(["date","hour"]);
		});
		
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('expenses');
	}

}
