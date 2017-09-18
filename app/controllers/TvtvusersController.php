<?php

class TvtvusersController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
	    return Tvtvuser::all();	
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
        
        $user = Tvtvuser::create( array(
            'activated'             => 0,
            'company_id'            => 0,
            'email'                 => Input::get('email'),
            'username'              => Input::get('username'),
            'password'              => Hash::make(Input::get('password_unencrypted')),
            'password_unencrypted'  => Input::get('password_unencrypted'),
            'first_name'            => '',
            'last_name'             => '',
            'remember_token'        => ''
        ));	
        
        // $video = Video::find(Input::get('video_id'));
        // //conver int to string
        // $user_id = (string)$user->id;
        // $video_users = $video->users.','.$user_id;
        // $video->users = $video_users;
        // $video->save();

        return $user;
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
	    return Tvtvuser::find($id);
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
	    $user = Tvtvuser::find($id);

        $user->username     = Input::get('username');
        $user->email        = Input::get('email');
        $user->password     = Hash::make(Input::get('password_unencrypted'));
        $user->password_unencrypted = Input::get('password_unencrypted');

        $user->save();
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
        Tvtvuser::find($id)->delete();
	}

}
