<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSourcesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sources', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->increments('id');
			$table->integer('version_id')->unsigned();
			$table->boolean('master')->default(0);
			$table->string('format', 5);
			$table->string('location', 50);
			$table->string('zip', 50)->default(NULL);
			$table->smallInteger('width');
			$table->smallInteger('height');
			$table->timestamps();
			$table->index('version_id');

			// $table->foreign('version_id')->references('id')->on('versions')->onDelete('restrict')->onUpdate('restrict');

			/*
			CREATE TABLE sources (
				id int unsigned not null auto_increment PRIMARY KEY,
				version_id int unsigned not null,
				master boolean not null default 0,
				format char(5),
				location varchar(50),
				zip varchar(50) DEFAULT null,
				width TINYINT(6),
				height TINYINT(6),
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
		Schema::drop('sources');
	}

}
