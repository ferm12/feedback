<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVersionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('versions', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->smallInteger('version')->unsigned();
			$table->string('review_stage', 7);
			$table->float('duration');
			$table->float('fpt');
			$table->unsignedInteger('video_id');
			$table->timestamps();

			$table->index('video_id');

			// $table->foreign('video_id')->references('id')->on('videos')->onDelete('restrict')->onUpdate('restrict');
			/*
			versions table ties updates in a video
			CREATE TABLE versions (
				id int unsigned not null auto_increment PRIMARY KEY,
				video_id int unsigned not null,
				review_stage char(7),
				version_number TINYINT(2) unsigned not null,
				duration FLOAT(10) not null,
				fps FLOAT(6) not null,
				created DATETIME DEFAULT CURRENT_TIMESTAMP,
				modified DATETIME DEFAULT NULL,
				INDEX (video_id),
				FOREIGN KEY (video_id) REFERENCES videos (id)
			) ENGINE=INNODB;
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
		Schema::drop('versions');
	}

}
