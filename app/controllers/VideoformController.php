<?php

date_default_timezone_set('America/Los_Angeles');

class VideoformController extends BaseController {

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

	public function videoForm()
	{
		return View::make('form');
    }

	public function videoUpload()
	{
        if (!empty($GLOBALS['_FILES'])){
            try {
       
                // $data = validateForm();
                $video_file = Input::file('video-file');

                if ($video_file->getError() !== 0) 
                    throw new Exception('There was an ERROR uploading the  file!');

                $new_video = Video::create(array());

                $video_id = $new_video->id;

                // $title = $data->metadata->getClientOriginalName();
                $title = $video_file->getClientOriginalName();
                //extract the movie type from the video file 
                $video_type = substr($title, strrpos($title, '.', -1));
                //extract the title  
                $title = substr($title, 0, strlen($title)-strlen($video_type));
                
                
                // replaces any non-alphabet or non-numberic character with underscore 
                // convers to lowercase
                $title = preg_replace('/[^\w]/', "_", strtolower($title));
                
                if ($video_id < 9)
                    $title = date('Ymd').'0'. $video_id.'_'.$title;
                else 
                    $title = date('Ymd'). $video_id.'_'.$title;

                $video_dir = dirname(dirname(dirname(__FILE__)))."/public/video_review/".$title.'/';
                if ( !is_dir( $video_dir ) )
                    mkdir( $video_dir, 0777, true );

                // $data->metadata->move( $video_dir, $title.$video_type );
                $video_file->move( $video_dir, $title.$video_type );

                $video = Video::find($video_id);

                $video->video_title     = $title; 
                $video->description     = Input::get('description');
                $video->url             = 'http://'.$_SERVER['HTTP_HOST'].'/feedback?video='.$title;
                $video->video_dir       = $video_dir;
                $video->save();	

            } catch (Exception $e) {
                $error = $e->getMessage();
            }
            $return_meta = new stdClass();
            $return_meta->id             = $video->id;
            $return_meta->video_title    = $video->video_title;
            $return_meta->description    = $video->description;
            $return_meta->url            = $video->url;
            $return_meta->video_type     = $video_type;

            return json_encode($return_meta);
        } 
    }
    public function startConversion()
    {
        $video_id = Input::get('video_id');
        $video_type = Input::get('video_type');
        $video = Video::find($video_id);
        
        $video_converting = new \Classes\Video($video_id, $video_type);
        $video_converting->verifyUpload();
        $video_converting->convertOriginal();
        $video_converting->zipOriginal();

        $video->duration        = $video_converting->getDuration();
        $video->fps             = $video_converting->getFps(); 
        $video->width           = $video_converting->getWidth();
        $video->height          = $video_converting->getHeight();
        $video->video_srcs      = $video_converting->getVideoSrcs();
        $video->project_id      = 1;
        $video->project_name    =  $video_converting->getTitle();
        $video->clients_ids     = '';
        $video->progress_file   = $video_converting->getProgressFile();
        $video->pid_file        = $video_converting->getPidFile();
        $video->total_frames    = $video_converting->getTotalFrames();
        $video->active          = 1;

        $video->save();	

        return $video;
    }
    
	public function videoConversion()
	{

        function getLastLine($file) {
            $line = '';

            #$file = fopen($path, 'r');
            $cursor = -1;

            fseek($file, $cursor, SEEK_END);
            $char = fgetc($file);

            /**
             * Trim trailing newline chars of the file
             */
            while ($char === "\n" || $char === "\r") {
                fseek($file, $cursor--, SEEK_END);
                $char = fgetc($file);
            }
            /**
             * Read until the start of file or first newline char
             */
            while ($char !== false && $char !== "\n" && $char !== "\r") {
                /**
                 * Prepend the new char
                 */
                $line = $char . $line;
                fseek($file, $cursor--, SEEK_END);
                $char = fgetc($file);
            }
            return $line;
        }
        /**
         * Test to see whether the process $pid is running.
         * If the shell returns the process id then we return true, false otherwise.    
         */
        function isRunning($pid){
            $result = shell_exec(sprintf("ps %d", $pid));
            if (preg_match("/([1-9])\d+/", $result)){
                return true;
            }else{
                return false;
            }
        }

        $progress_file_path = Input::get('progress');
        $pid_file = Input::get('pid');
        $frames = (int) Input::get('frames');
        
        $result = new stdClass();
        
        $pid = trim(file_get_contents($pid_file));
        $progress_file_total_contents = trim(file_get_contents($progress_file_path));

        if (isRunning($pid)) {
            $progress_file_pointer = fopen($progress_file_path, 'r');
            $progress_file_last_line = getLastLine($progress_file_pointer);
            $finished = false;
            $result->lastLine =$progress_file_last_line;
                
            if (preg_match('/^frame=\s*(\d+)/', $progress_file_last_line, $matches)) {
                $rendered = (int) $matches[1];
                $result->rendered = $rendered;
                $result->frames = $frames;
                if ($rendered >= $frames) {
                    $percentage = 100;
                    $finished = true;
                } else {
                    $percentage = ($rendered / $frames) * 100;
                }
            } elseif (preg_match('/(video:\d+kB audio:\d+kB)/', $progress_file_total_contents, $matches)) {
                $percentage = 100;
                $finished = true;
            } else {
                $result->errors = "Could not parse the progress";
                $result->fatal = false;
                $percentage = 0;
            }
        } else {
            if (preg_match('/(video:\d+kB audio:\d+kB)/', $progress_file_total_contents, $matches)) {
                $finished = true;
                $percentage = 100;
                $rendered = $frames;
            } else {
                $percentage = 0;
                $finished = false;
                $result->errors = "An error occured while converting";
                $result->fatal = true;
            }
        }

        $result->percent_complete = $percentage;
        $result->finished = $finished;
        
        return json_encode($result);

	}
}
