<?php

class ThumbnailsTableSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();
		
		$thumbnails = array(
            array(
            )
		);
		
		DB::table('thumbnails')->insert($thumbnails);
	}
}

