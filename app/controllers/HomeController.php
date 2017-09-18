<?php

class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWecome');
	|
	*/
	protected $layout = 'master';


	public function index()
	{
		return View::make('home.index');
    }

    public function showLogin()
    {
        return View::make('home.login'); 
    }

    public function doLogin()
    {
        $email = Input::get('email');
        $password = Input::get('password');

        if ( preg_match('/@/m', $email) ){
            $userdata = array(
                'email' => $email,
                'password'=> $password
            );
        }else{
            $userdata = array(
                'email' => $email.'@transvideo.com',
                'password' => $password
            );
        }
        // validate the info, create rules for the inputs
        $rules = array(
            'email'    => 'required|email', // make sure the email is an actual email
            'password' => 'required|alphaNum|min:2' // password can only be alphanumeric and has to be greater than 3 characters
        );

        // run the validation rules on the inputs from the form
        $validator = Validator::make($userdata, $rules);

        // if the validator fails, redirect back to the form
        if ($validator->fails()) {
            return Redirect::to('login')
                ->withErrors($validator) // send back all errors to the login form
                ->withInput(Input::except('password')); // send back the input (not the password) so that we can repopulate the form
        } else {
            // create our user data for the authentication
            // $userdata = array(
            //     'email'     => Input::get('email'),
            //     'password'  => Input::get('password')
            // );
            //

            if ( Auth::tvtvuser()->attempt($userdata) ){

                return Redirect::intended('admin');   

            }elseif ( Auth::client()->attempt($userdata) ){
                // var_dump(Auth::client()->get());
                // $id = Auth::client()->id;
                // $video = DB::table('videos')->where('clients_ids',$id);
                // return Redirect::intended($video[0]->url);
                // return Redirect::intended('client', array('client'=> Auth::client()->get()) );
                //
                return Redirect::action('HomeController@client', array('c'=>Auth::client()->get()->id ));

            }else{
                //validation not successful, send back to form 
                return Redirect::to('login');
            }
            
            // attempt to do the login either tvtvuser or client
            // if ( Auth::tvtvuser()->attempt($userdata) || Auth::client()->attempt($userdata) ) {

            //     if ( Auth::tvtvuser()->attempt($userdata) )
            //         return Redirect::intended('admin');     //redirects to intemded url admin otherwise
            //     else
            //         return Redirect::intended('notFound');
                    
            // if (true){
                // validation successful!
                // redirect them to the secure section or whatever
                // return Redirect::to('secure');
                // for now we'll just echo success (even though echoing in a controller is bad)
                // return Redirect::to('upload_form');
                
                // redirect the user back to the intended page
                // or defaultpage if there isn't one
                // if (Auth::attempt($credentials)) {
                    // return Redirect::intended('admin');
                // }
            // }else{
                // validation not successful, send back to form 
            //     return Redirect::to('login');
            // }
        }
    }

    public function doLogout()
    {
        Auth::client()->logout();       // logout the client

        Auth::tvtvuser()->logout();     // logout the tvtvuser

        Session::flush();

        return Redirect::to('login'); // redirect the user to the login screen
    }

    public function admin()
    {
        return View::make('admin');
    }

    public function tvUsers()
    {
        return View::make('tvUsers');
    }

    public function client()
    {
        return View::make('client');
    } 

    public function notFound()
    {
        return View::make('404');
    }

}
