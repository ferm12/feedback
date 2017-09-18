<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

// Route::get('/hello', function()
// {
// 	return View::make('hello');
// });

/* Route::resource('companies', 'CompaniesControler'); */

// route to show the login form
Route::get('/', array('uses' => 'HomeController@showLogin'));
Route::get('/login', array('uses' => 'HomeController@showLogin'));

// route to process the form
Route::post('/login', array('uses' => 'HomeController@doLogin'));
//
Route::get('/notFound', array('uses' => 'HomeController@notFound'));

//the logout redirects the user to the login page
Route::get('/logout', array('uses' => 'HomeController@doLogout'));
// Route::get('/tvusers', 'TvusersController@index');
Route::get('/tvusers', 'HomeController@tvUsers');
Route::get('client', "HomeController@client");

Route::resource('comments','CommentsController');
Route::resource('comments.video','CommentsController');
Route::resource('notes', 'NotesController');
Route::resource('notes.video', 'NotesController');
Route::resource('svgs', 'SvgsController');
Route::resource('svgs.video', 'SvgsController');
Route::resource('thumbnails', 'ThumbnailsController');
Route::resource('thumbnails.video', 'ThumbnailsController');

Route::resource('tvtvusers', 'TvtvusersController');
Route::resource('clients', 'ClientsController');
Route::resource('clients.video', 'ClientsController');

// Route::resource('draws.video', 'DrawsController');

Route::resource('videos', 'VideosController');

// Route::resource('progress', 'ProgressController');
Route::get('videoconversion', 'VideoformController@videoConversion');
Route::get('startconversion', 'VideoformController@startConversion');

Route::post('videoupload', 'VideoformController@videoUpload');

// routes the uploaded file 
Route::post('attachfile', 'FileController@attachFile');
Route::post('detachfile', 'FileController@detachFile');


// Route::resource('users', 'UsersController');
//Route::resource('notes', 'NotesController');
// Route::resource('projects', 'ProjectsController');
// Route::resource('sources', 'SourcesController');
// Route::resource('users', 'UsersController');
// Route::resource('versions', 'VersionsController');
// Route::resource('videos', 'VideosController');

// Route::get('login', array('uses' => 'HomeController@showLogin'));
// Route::get('login', array('uses' => 'HomeController@doLogin'));

// run the auth filter
Route::group(array('before' => 'auth'), function()
{
    Route::get('/feedback', 'HomeController@index');
    Route::get('/viewpdf', 'ViewpdfController@viewPdf');
    // Route::get('/dompdf', 'ViewpdfController@dompdf');
    // Route::get('/upload_form', 'UploadformController@index');
    // Route::post('/uploadedvideos', 'UploadedvideosController@index');
    // Route::resource('/uploaded_videos', 'UploadedvideosController@index');
    Route::get('videoform', 'VideoformController@videoForm');
    Route::get('admin', 'HomeController@admin');
});

// Route::get('/edit_user', 'HomeController@useredit');

// Event::listen('laravel.query', function ($sql){
// 	var_dump($sql);
// });

// Route::get('/', function(){
// 
// 
// });

//Route::get('/', function(){
	
	// $note = new Note(array('note' => 'first note'));

	// $cuepoint = CuePoint::find(1);

	// $cuepoint->note()->save($note);
	
/*
	$post = new Post(array(
		'title' => 'mst',
		'body' => 'body for the post'
	));

	$author = Author::find(1);

	$author->posts()->save($post);
	dd(Autho);

 */

//});
/*
|-----------------------------------------------------------------------
| Route Filters
|-----------------------------------------------------------------------
| Filters provide a convenient method for attaching functionality to your
| routes. The built-in before and after filters are called before and after
| everty request to your application, and you may even create other filters
| that can be attached to individual routes. 
|
| Let's walk through an example..
|
| First, defienea filter:
|
|       Route::filter('filter', function()
|       {
|           return 'Filtered';
|       });
|
| Next, attach the filter to a route:
|
|       Route:register('GET /', array('before' => 'filter', function()
|       {
|           return 'Hello World!';
|       }));
|
*/

Route::filter('before', function()
{
    //do stuff before every request to your application..
});

Route::filter('after', function($response)
{
    //do stuff after every request to your application..
});

Route::filter('csrf', function()
{
    if (Request::forged()) 
        return Response::error('500');
});

Route::filter('auth', function()
{
    // if (Auth::tvtvuser()->guest())
    //     return Redirect::guest('login');
    if (Auth::tvtvuser()->guest() && Auth::client()->guest())

        return Redirect::guest('login');
        // return Redirect::to('login');
});

/*
 * CRUD maps to REST like:
 *
 * create  → POST      /collection
 * read    → GET       /collection[/id]
 * update  → PUT       /collection/id
 * patch   → PATCH     /collection/id
 * delete  → DELETE    /collection/id
 */



