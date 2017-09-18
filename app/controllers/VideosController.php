<?php

class VideosController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        return Video::all();
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
        // $video = Video::find(Input::get('video_id'));

        // $video->video_title     = Input::get('video_title');
        // $video->duration        = Input::get('duration');
        // $video->fps             = Input::get('fps');
        // $video->width           = Input::get('width');
        // $video->height          = Input::get('height');
        // $video->description     = Input::get('description');
        // $video->video_dir       = Input::get('video_path');
        // $video->video_srcs      = Input::get('video_srcs');
        // $video->url             = Input::get('url');
        // $video->project_id      = Input::get('project_id');
        // $video->project_name    = Input::get('project_name');
        // $video->clients_ids     = '';
        // $video->active          = 1;

        // $video->save();	
        // return $video;
        
        // $file = Input::file('video-file');
        // $file_name = $file->getClientOriginalName();
        // $path = public_path().'/videos';
        // $file->move($path, $file_name);
        // $file_name = explode('.', $file_name);

        // $value  = '<h1>Video Saved</h1><br/>';
        // $value .= 'View video: <a href="http://'.$_SERVER['HTTP_HOST'].'/laravel_video_feedback_v2/public/index.php?video='.$file_name[0].'" a>http://'.$_SERVER['HTTP_HOST'].'/laravel_video_feedback_v2/public/index.php?video='.$file_name[0].'</a>';
        // return $value;
	}

	/**s
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
	    return	Video::find($id);
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
        $video = Video::find($id);

	    $video->active = Input::get('active');
        $video->save();
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
        $root = dirname(dirname(dirname(__FILE__)));
        $video = Video::find($id); // $thumbnail represents the thumbnail table as an obj 
        $video_dir = $root.'/public/video_review/'.$video->video_title;
        
        // force remove video_dir
        exec("rm -rf ".$video_dir);
        
        // deletes video and comments from the database
        $video->delete();
        DB::table('comments')->where('video_id', $id)->delete();
	}
}
/*
 
CRUD maps to REST like so:

create  → POST      /collection
read    → GET       /collection[/id]
update  → PUT       /collection/id
patch   → PATCH     /collection/id
delete  → DELETE    /collection/id

*/

