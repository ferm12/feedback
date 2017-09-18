<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('clients', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';	

            $table->increments('id');
			$table->boolean('activated')->nullable()->default(0);
			$table->integer('company_id')->nullable()->unsigned();
			$table->string('email', 30)->nullable();
			$table->string('username', 30)->nullable();
			$table->string('password', 70);
            $table->string('password_unencrypted')->nullable();
			$table->string('first_name', 30)->nullable();
			$table->string('last_name', 30)->nullable();
            $table->integer('video_id')->nullable();
            $table->string('remember_token', 100)->nullable();

			$table->index('company_id');
			$table->unique('email');
			$table->unique('username');
			// $table->timestamps(); commented for now just now...


			// $table->foreign('company_id')->references('id')->on('companies')->onDelete('restrict')->onUpdate('restrict');

			/*
			REATE TABLE users (
				id int unsigned not null auto_increment PRIMARY KEY,
				activated boolean not null default 0,
				company_id int unsigned not null,
				email varchar(30),
				username varchar(30),
				password char(60), # bcrypt hashing
				first_name varchar(30),
				last_name varchar(30),
				created DATETIME DEFAULT CURRENT_TIMESTAMP,
				modified DATETIME DEFAULT NULL,
				INDEX (company_id),
				FOREIGN KEY (company_id) REFERENCES companies (id),
				UNIQUE (email),
				UNIQUE (username)
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
		Schema::drop('clients');
	}
}
