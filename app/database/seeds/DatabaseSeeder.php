<?php

class DatabaseSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();
		
		// $this->call('CuePointsTableSeeder');

		// $this->call('NotesTableSeeder');

		// $this->call('ProjectsTableSeeder');
        
        // $this->call('SourcesTableSeeder');
        
        // $this->call('SvgsTableSeeder');
        
		// $this->call('ThumbnailsTableSeeder');
        
		$this->call('TvtvusersTableSeeder');

        // $this->call('VersionsTableSeeder');
        
        // $this->call('VideosTableSeeder');
	}
}
