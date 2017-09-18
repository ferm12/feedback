<?php

class FileController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/
	protected $layout = 'master';

	// public function index()
	// {
	// 	return View::make('uploadform.index');
    // }
    public function attachFile() 
    {
        // get the file title and mime_type;
        // var_dump($GLOBALS['_FILES']);

        $file           = Input::file('attach-file');         
        $title          = $file->getClientOriginalName();
        $file_extension = substr($title, strrpos($title, '.', -1));
        $title          = substr($title, 0, strlen($title)-strlen($file_extension));
        
        // convert to lowercase and replace spaces and hyphens for underscore
        $title = preg_replace('![^\w]!', "_", strtolower($title));

        $type  = explode("/", $file->getClientMimeType());  
        // defiening the type
        if ( $type[0] == 'application' )
            $type = $type[1];
        else
            $type = $type[0];

        $file_dir = dirname(dirname(dirname(__FILE__)))."/public/video_review/".Input::get('video_title').'/';
        $file_url = Input::get('video_title')."/$title".$file_extension;

        // move the file from the temp folder to file_dir
        $file->move( $file_dir, $title.$file_extension );

        // get the comment
        $comment_id = Input::get('comment_id');
        $comment = Comment::find($comment_id);

        // check if the attachements field is empty to avoid inserting unecessary commas.
        if ( empty($comment->attachments) ){
            $comment->attachments = $file_url.'('.$type.')';
        }else{
            $attachments = $comment->attachments.','.$file_url.'('.$type.')';
            $comment->attachments = $attachments;
        }
        $comment->save();
        
        $return_obj             = new StdClass();
        $return_obj->file_url   = $file_url;
        $return_obj->type       = $type;
        $return_obj->file_name  = str_replace(".", "_", $title.$file_extension);
        
        return json_encode($return_obj);
    }

    public function detachFile(){
        $file = Input::get('file');

        $attachments = DB::table('comments')->select(DB::raw('*'))->where('attachments', 'like', '%'.$file.'%')->get();
        $comment = Comment::find($attachments[0]->id);

        $attachments = $attachments[0]->attachments;
        $attachments = explode(',', $attachments);
        $attach_dir = explode("/", $attachments[0]);
        $file_dir = dirname(dirname(dirname(__FILE__)))."/public/video_review";


        // puts attachments in an array and loops through it to delete the file
        for ($i = 0; $i < count($attachments); $i++){
            if ( preg_match("/".$file."/m", $attachments[$i]) ){
                unset( $attachments[$i] );
            }
        }
        $attachments = implode(",", $attachments);
        
        // Save 
		$comment->attachments = $attachments;
        $comment->save();

        foreach( array_diff(scandir($file_dir.'/'.$attach_dir[0]), array('.', '..')) as $scan_file ) {
            if ($scan_file == $file)
                unlink($file_dir.'/'.$attach_dir[0].'/'.$scan_file); //delete file
        }

        return "File detached successfully!";
    }
}

