<?php

class SvgsTableSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run(){

		Eloquent::unguard();
		
		$svgs = array(
            array(
            )
		);

		DB::table('svgs')->insert($svgs);
	}
}
