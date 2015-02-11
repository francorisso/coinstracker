<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

		$this->call( "OAuthClientsTableSeeder" );
		$this->command->info('Oauth Clients table seeded!');
	}

}

class OAuthClientsTableSeeder extends Seeder {
	public function run(){
		DB::table("oauth_clients")->delete();
		DB::table("oauth_clients")->insert([
			'id' 		=> 'webapp',
			'secret' 	=> 'CV9e8WbKT8Xe9T4FBi3RyF1J6eIJxwpt',
			'name'		=> 'Official Web App'
		]);
	}
}