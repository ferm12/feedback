<?php

class NotesTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();
		
		$notes = array(
            array(
                'note' => '00:00:00',
                'cuepoint_id' => '1'
            )
		);
		
		DB::table('notes')->insert($notes);

	}
}
