<?php

class VideosTableSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();
		
		$videos = array(
			array(
				'video_title'   => 'chimpped_demo', 
                'review_stage'  => 'rough0',
                'duration'      => 97.768,
                'fps'           => 23.98,
                'width'         => 640,
                'height'        => 360,
				'description'   => 'Chimpped Demo Description',
                'video_path'    => 'video_path',
                'srcs'          => 'chimpped.mp4, chimpped.webm',
                'url'           => 'url'
                'project_id'    => 1,
                'project_name'  => 'chimpped_demo_project'
			)
        );

		DB::table('videos')->insert($videos);
        
	}

}
