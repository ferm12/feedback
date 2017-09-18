<?php

class CuePointsTableSeeder extends Seeder {
	// Run the database seeds.
	// @return void

	public function run()
	{
		Eloquent::unguard();

		$cuepoints = array(
			array( 'cuepoint' => '00:00:00','cuepoint_seconds' => 0.0 )
		);

		DB::table('cuepoints')->insert($cuepoints);
	}
}

