<?php

class ClientsTableSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

        $clients= array(
            array(
				'activated'     => 1, 
                'company_id'    => 0,
                'email'         => 'client1@transvideo.com',
                'username'      => 'client1',
                'password'      => Hash::make('client1'),
                'password_unencrypted' => 'client1',
                'first_name'    => 'client1',
             'last_name'     => 'client1',
                'remember_token'=> ''
            ),
		);

		DB::table('clients')->insert($clients);
	}

}
