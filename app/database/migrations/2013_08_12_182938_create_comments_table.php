<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('comments', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('parent_id')->nullable();
			$table->string('cuepoint')->nullable();
			$table->float('cuepoint_seconds', 10)->nullable();
            $table->text('comment')->nullable();
            $table->string('thumbnail')->nullable();
            $table->text('svg')->nullable()->nullable();
            $table->integer('video_id');
            $table->text('user');
            $table->text('attachments')->nullable();
			$table->timestamps();

            // $table->increments('id');
            // $table->integer('parent_id')->nullable()->default(NULL);
			// $table->string('cuepoint')->nullable()->default(NULL);
			// $table->float('cuepoint_seconds', 10);
            // $table->integer('video_id');
			// $table->unsignedInteger('version_id')->nullable()->default(NULL);
			// $table->timestamps();
			// $table->index('version_id');
			// $table->foreign('version_id')->references('id')->on('versions')->onDelete('restrict')->onUpdate('restrict');
			/*
			CREATE TABLE cue_points (
				id int unsigned not null auto_increment PRIMARY KEY,
				version_id int unsigned not null,
				in_poin FLOAT(10) not null,
				out_point FLOAT(10) DEFAULT null,
				created DATETIME DEFAULT CURRENT_TIMESTAMP,
				modified DATETIME DEFAULT NULL,
				INDEX (version_id),
				FOREIGN KEY (version_id) REFERENCES versions (id)
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
		Schema::drop('comments');
	}

}

