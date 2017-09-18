<?php

class TvtvusersTableSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

        $tvtvusers = array(
            array(
				'activated'     => 1, 
                'company_id'    => 0,
                'email'         => 'admin@transvideo.com',
                'username'      => 'admin',
                'password'      => Hash::make('admin'),
                'password_unencrypted' => 'admin',
                'first_name'    => 'admin_first',
                'last_name'     => 'admin_last',
                'remember_token'=> ''
            ),
		);

		DB::table('tvtvusers')->insert($tvtvusers);
	}

}
