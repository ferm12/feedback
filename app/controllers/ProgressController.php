<?php

class ProgressController extends BaseController {
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        // header('Content-type: application/json');

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
            $finished = true;
            if (preg_match('/(video:\d+kB audio:\d+kB)/', $progress_file_total_contents, $matches)) {
                $percentage = 100;
                $rendered = $frames;
            } else {
                $result->errors = "An error occured while converting";
                $result->fatal = true;
            }
        }

        $result->percent_complete = $percentage;
        $result->finished = $finished;
        
        return json_encode($result);

	}

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
    /*
	protected $layout = 'master';

	public function index()
	{
		return View::make('progress.index');
    }
     */
}
