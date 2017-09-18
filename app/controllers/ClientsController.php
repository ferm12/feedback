<?php

class ClientsController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
	    return Client::all();	
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
        
        $client = Client::create( array(
            'activated'             => 0,
            'company_id'            => 0,
            'email'                 => Input::get('email'),
            'username'              => Input::get('username'),
            'password'              => Hash::make(Input::get('password_unencrypted')),
            'password_unencrypted'  => Input::get('password_unencrypted'),
            'first_name'            => '',
            'last_name'             => '',
            'video_id'              => Input::get('video_id'),
            'remember_token'        => ''
        ));	

        $video_id = Input::get('video_id');
        $video = Video::find($video_id);
        //conver int to string
        $client_id = (string) $client->id;
        if ( empty($video->clients_ids) ){
            $video->clients_ids = $client_id;
        }else{
            $video_users = $video->clients_ids.','.$client_id;
            $video->clients_ids = $video_users;
        }
        $video->save();

        return $client;
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show( $id, $video = null )
	{
	    // return Client::find($id);
        if ($video == null)
            return Client::find($id);
        else
            return Client::where('video_id', $video)->get();

	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
        $client = Client::find($id);

        $client->username     = Input::get('username');
        $client->email        = Input::get('email');
        $client->password     = Hash::make(Input::get('password_unencrypted'));
        $client->password_unencrypted = Input::get('password_unencrypted');

        $client->save();
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
        Client::find($id)->delete();
	}

}
