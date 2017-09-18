<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVideosTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('videos', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			
			$table->increments('id');
			$table->string('video_title', 50)->nullable();
            // $table->smallInteger('review_stage');
            $table->float('duration')->nullable();
            $table->float('fps')->nullable();
            $table->smallInteger('width')->nullable()->unsigned();
            $table->smallInteger('height')->nullable()->unsigned();
            $table->text('description')->nullable();
            $table->text('video_dir')->nullable();
            $table->text('video_srcs')->nullable();
            $table->text('url')->nullable();
			$table->smallInteger('project_id')->nullable()->unsigned();
            $table->text('project_name')->nullable();
            $table->text('clients_ids')->nullable();
            $table->boolean('active')->nullable();
            $table->text('progress_file')->nullable();
            $table->text('pid_file')->nullable();
            $table->integer('total_frames')->nullable();

			$table->timestamps();

			// $table->foreign('project_id')->references('id')->on('projects')->onDelete('restrict')->onUpdate('restrict');

			/*
			CREATE TABLE videos (
				id int unsigned not null auto_increment PRIMARY KEY,
				title varchar(50),
				description text,
				project_id int unsigned not null,
				created DATETIME DEFAULT CURRENT_TIMESTAMP,
				modified DATETIME DEFAULT NULL,
				INDEX (project_id),
				FOREIGN KEY (project_id) REFERENCES projects (id)
			) ENGINE=INNODB
			*/		
	
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('videos');
	}

}
