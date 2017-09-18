<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('projects', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->increments('id');
			$table->string('name',50);
			$table->integer('company_id');
			$table->smallInteger('invoiced');
			$table->timestamps();
			$table->index('company_id');
			$table->unique('name');

			// $table->foreign('company_id')->references('id')->on('companies')->onDelete('restrict')->onUpdate('restrict');

			/*
			CREATE TABLE projects (
				id int unsigned not null auto_increment PRIMARY KEY,
				name varchar(50),
				company_id int unsigned not null,
				invoiced boolean not null default 0,
				created DATETIME DEFAULT CURRENT_TIMESTAMP,
				modified DATETIME DEFAULT NULL,
				
				INDEX (company_id),
				FOREIGN KEY (company_id) REFERENCES companies (id),
				UNIQUE (name)
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
		Schema::drop('projects');
	}

}
