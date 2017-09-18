<?php

    /*
     * Date used by all php variables and functions
     */
    date_default_timezone_set('America/Los_Angeles');
/*
    //auto loads the class folder
    function classLoader($class) {
        // $file = $_SERVER['DOCUMENT_ROOT'].'/classes/' . $class . '.php';
        // $file = dirname(dirname(__FILE__)).'/classes/' . $class . '.php';
        $file = app_path(). '/videoconversion/classes'. $class . '.php';
        require_once($file);
    }
    spl_autoload_register('classLoader');

 */
    # Check that all nesecary form data was submitted and return an object to pass as an argument for a Video instance
// var_dump(empty(Input::get('title')));
// if (Input::has('name')){
//     echo "has name";
// }else{
//     echo "has no name";
// }
// var_dump(Input);
    function validateForm() {
        // if ( isset(Input::file('video-file')) ){
        if (!empty($GLOBALS['_FILES']))
            $video_file = Input::file('video-file');
        // }else{
        //     echo 'Error: Global $_FILES variable not set. Posible problem, php.ini file_size limits';
        //     return;
        // }
        if ($video_file->getError() !== UPLOAD_ERR_OK) {
            throw new UploadException($video->getError() );
        }
        
        $title = Input::get('video-title');
        if ($title === "") {
            throw new Exception("You need to include a title.");
        }
        
        // $project = Input::get('project');
        // if ($project === "") {
        //     throw new Exception("You need to select a project.");
        // }
        
        // $review_stage = Input::get('review_stage');
        // if ($review_stage === "") {
        //     throw new Exception("You need to select a review stage.");
        // }
        
        // $version = (float) Input::get('version');
        
        if (Input::get('version') === "") {
            throw new Exception("You need to enter a review version.");
        }
        
        $description = Input::get('description');
        if ($description === "") {
            throw new Exception("You need to enter a review version.");
        }

        //gather the video metadata 
        $video = new stdClass();

        $video->metadata = $video_file;
        // $data->project = $project;
        // $data->review_stage = $review_stage;
        // $data->version = $version;
        $video->description = $description;

        /*
         * Check for video existence, add 1 to the end of date prefix, if video already exist to avoid duplicates
         */
        // for ($i=1; $i<10 ;$i++){
        //     $video_title = date('Ymd').'0'.$i.'_'.$video->getClientOriginalName();
        //         // $video_title = $video->getClientOriginalName();
        //     if ( videoExist( $video_title ) ){
        //         continue;
        //     }else{
        //         //breaks out to the for loop
        //         $video_title =  explode('.', $video_title);
        //         $data->title = $video_title[0];
        //         break;
        //     }
        // }
        
        return $video;
    }
    /*
     * Check if video already esist
     */
    // function videoExist( $video_title ){
    //     $videos_dir = dirname(dirname(dirname(dirname(__FILE__))))."/public/uploads/";
    //     foreach( array_diff( scandir( $videos_dir ), array('.', '..') ) as $file ){
    //         if ( $file == $video_title ) {
    //             return true;
    //         }
    //     }
    //     return false;
    // }
    
    if (!empty($GLOBALS['_FILES'])){
        try {
   
            $data = validateForm();
            //php namespace is used to find Video class located at app/videoconversion/classes 
            $video = new \Classes\Video($data);
            $video->verifyUpload();
            $video->convertOriginal();
            $video->zipOriginal();
            $video->save();
            
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    } 
?>

@extends('master')
@section('content')

    <style type="text/css" media="screen">
        #addVideo {margin-bottom:2em;}
        td {padding-left:5px; border-right:1px solid #bcb3a6;}
        .bb{border-bottom:none;}
        .progress .progress-bar{font-size:16px;font-weight:bold}
    </style>


    <div id="page-wrapper">

        <b>
            @if (Auth::tvtvuser()->check()) 

                {{ Auth::tvtvuser()->get()->email }}

            @elseif (Auth::client()->check())

                {{ Auth::client()->get()->email }}

            @endif 
        </b></br>

        <a class="btn btn-default" href="{{ URL::to('logout') }}">Logout</a>
        <?php if ( isset($error) ): ?>

            <h1><?php echo $error; ?></h1>

        <?php else: ?>

            <h1>Conversion In Progress</h1>
            <p>Your video has been uploaded and is currently being converted to HTML5 video formats (.mp4 and .webm).</p>

            <div class="progress"><div id="progress-bar" class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div></div>

        <?php endif;?>

        <?php if(!empty($GLOBALS['_FILES'])): ?>
        <!-- Here we gather all the video info that backbone will be saving to the databes -->
            <input type="hidden" id="video_id" value="<?php echo $video->getId(); ?>">
            <input type="hidden" id="video_title" value="<?php echo $video->getTitle(); ?>">
            <input type="hidden" id="duration" value="<?php echo $video->getDuration(); ?>">
            <input type="hidden" id="fps" value="<?php echo $video->getFps(); ?>"></input>
            <input type="hidden" id="width" value="<?php echo $video->getWidth(); ?>"></input>
            <input type="hidden" id="height" value="<?php echo $video->getHeight(); ?>"></input>
            <input type="hidden" id="description" value="<?php echo $video->getDescription(); ?>"></input>
            <input type="hidden" id="video_path" value="<?php echo 'video_review/'.$video->getTitle(); ?>"></input>
            <input type="hidden" id="video_srcs" value="<?php echo $video->getVideoSrcs(); ?>"></input>
            <input type="hidden" id="url" value="<?php echo 'http://'.$_SERVER['HTTP_HOST'].'/feedback?video='.$video->getTitle(); ?>"></input>
            <input type="hidden" id="project_id" value="<?php echo '1'; ?>"></input>
            <input type="hidden" id="project_name" value="<?php echo $video->getTitle();?>"></input>

            <input type="hidden" id="progress_file" value="<?php echo $video->getProgessFile(); ?>"></input>
            <input type="hidden" id="pid_file" value="<?php echo $video->getPidFile(); ?>"></input>
            <input type="hidden" id="frame_count" value="<?php echo $video->getFrameCount(); ?>"></input>

        <?php endif; ?>
                
        <table id="allVideos">
            <thead>
                <tr>
                    <td>Video Title</td>
                    <td>Duration(sec)</td>
                    <td>Description</td>
                    <td style="border-right:none">Url</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="bb"><?php echo $video->getTitle(); ?></td>
                    <td class="bb"><?php echo $video->getDuration(); ?></td>
                    <td class="bb"><?php echo $video->getDescription(); ?></td>
                    <td class="bb" style="border-right:none;"><a href="<?php echo 'http://'.$_SERVER['HTTP_HOST'].'/feedback?video='.$video->getTitle(); ?>"><?php echo 'http://'.$_SERVER['HTTP_HOST'].'/feedback?video='.$video->getTitle(); ?></a></td>
                </tr>
            </tbody>
            </table>
        <!-- <div id="editVideo"></div> -->
    </div><!-- end #page-wrapper -->    






<!-- this templates are not in user right now -->
    <!-- <div class="tasks"> -->
    <script id="taskTemplate" type="text/template">
        <span><%= title %></span><button class="edit">Edit</button> <button class="delete">Delete</button>
    </script>

    <!-- </div> -->
    <script id="allVideosTemplate" type="text/template">
        <td class='bb'><%= video_title %></td>
        <td class='bb'><%= duration %></td>
        <td class='bb'><%= description %></td>
        <td class='bb' style='border-right:none;'><a href='<%= url %>'><%= url %></a></td>
    </script>
    <!-- <td class='bb'><%= version %></td> -->

    <!-- <td><a href="#videos/<%= id%>/edit" class="edit">Edit</a></td> -->
    <!-- <td><a href="#videos/<%= id%>" class="delete">Delete</a></td> -->
    <script id="editVideoTemplate" type="text/template">
        <form id='editVideo'>
            <div>
                <label for="edit_first_name">Video: </label>	
                <input type="text" id="edit_first_name" name="edit_first_name" value= "<%= first_name%>">
            </div>

            <div>
                <label for="edit_last_name"> Name: </label>	
                <input type="text" id="edit_last_name" name="edit_last_name" value = "<%= last_name %>">
            </div>

            <div>
                <label for="edit_email_address">Email address: </label>	
                <input type="text" id="edit_email_address" name="edit_email_address" value = "<%= email_address%>">
            </div>

            <div>
                <label for="edit_description">Description: </label>	
                <textarea id="edit_description" name="edit_description"><%= description %></textarea>
            </div>
            <div>
                <input type="submit" value="Add">
                <button type="button" class="cancel">Cancel</button>
                
            </div>
        </form>
    </script>

    <!-- backbone framework-->
	<!-- <script src="js/main.js"></script> -->
	<!-- <script src="js/models/models&#45;videos.js"></script> -->
	<!-- <script src="js/collections/collections&#45;videos.js"></script> -->
	<!-- <script src="js/views/views&#45;videos.js"></script> -->
	<!-- <script src="js/router.js"></script> -->
    <script src="js/controllers/controllers-videos.js"></script>    

@stop
