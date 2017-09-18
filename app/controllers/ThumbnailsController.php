<?php

class ThumbnailsController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return Thumbnail::all();
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
        $id = Input::get('cuepoint_id');
        $cuepoint = Cuepoint::find($id);

        $video_title = Input::get('video_title');

        $cuepoint_formatted = $cuepoint->cuepoint;
        $cuepoint_formatted[strrpos($cuepoint_formatted,':')] = '_';

        $cuepoint_seconds = $cuepoint->cuepoint_seconds;

        $app_root = dirname(dirname(dirname(__FILE__)));
        $video_dir = $app_root.'/public/video_review/'.$video_title.'/';
        // $imgs_dir = $app_root.'/public/uploads/imgs/'.$video_title.'/';

        /*
         * If dir for this video title doesn't exist create it
         */
        // if ( !is_dir( $imgs_dir ) )
        //     mkdir( $imgs_dir, 0777, true );
        
        if ( !is_dir( $video_dir ) )
            mkdir( $video_dir, 0777, true );

        // $cmd = 'ffmpegthumbnailer -i /Library/WebServer/Documents/laravel_video_feedback_v1/public/videos/chimpped.mp4 -o /Library/WebServer/Documents/laravel_video_feedback_v1/public/img/chimmped_test1.png -s 440 -t 00:00:50'
        $cmd = '/usr/local/bin/ffmpeg -i '.$video_dir.$video_title.'.mp4 -ss '.$cuepoint_seconds.' -f image2 -vframes 1 -s 440x247 '.$video_dir.$video_title.'_frame_'.$cuepoint_formatted.'.png';

        $shell_response = shell_exec($cmd);
        // if (preg_match('Conversion failed!', $shell_response, $matches)) {
        //     // $meta = $matches[0];
        //     return 'Capturing frame failed!!';
        // } 

        // $thumbnail_url = 'chimpped'.$cuepoint_formatted.'.png';        
        $thumbnail = new Thumbnail(array(
            'thumbnail' => $video_title.'/'.$video_title.'_frame_'.$cuepoint_formatted.'.png',
            'video_id'  => Input::get('video_id')
        ));
        $cuepoint_thumbnail = $cuepoint->thumbnail();
        //
        return $cuepoint_thumbnail->save($thumbnail); 
        // return $shell_response;
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id, $video = null)
	{
        if ($video == null)
            return Thumbnail::find($id);
        else
            return Thumbnail::where('video_id', $video)->get();
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
		// $thumbnail = Thumbnail::find($id);
        // $thumbnail->thumbnail = Input::get('thumbnail');
		// $thumbnail->save();

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
        $thumbnail = Thumbnail::find($id); // $thumbnail represents the thumbnail table as an obj 
        $video_name = explode('/', $thumbnail->thumbnail); // [chimpped, chimpped_00.png]
        $thumbnail_name = $video_name[1];
        $video_review_dir = $root.'/public/video_review/'.$video_name[0];

        //$img_path = substr_replace($img_path, '', $app_pos) . 'public/img/' . $thumbnail_explode[0]; // /Library/WebServer/Documents/laravel_video_feedback_v2/

        // $thumbnail =  Thumbnail::find($id);
        // return $thumbnail->thumbnail; // img/chimpped_thumbs/chimpped_00_01.png 
        // $explode_thubnail = explode('/', $thumbnail) // [img, chimpped_thumbs, chimpped_00_01.png]
        //                                             //  [   0,           1,          2]

        foreach( array_diff( scandir( $video_review_dir ), array('.', '..') ) as $file ){
            if ($file == $thumbnail_name) {
                unlink( $video_review_dir.'/'.$file );
            }
        }

        $thumbnail->delete();

	}

}

