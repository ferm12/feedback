<?php

class DrawsController extends BaseController {

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
        $img = Input::get('draw');
        $video_name = Input::get('video_name'); 
        $cuepoint = Input::get('cuepoint');
        $cuepoint = str_replace(':', '_', $cuepoint);
        $img_tmp = $video_name . '_tmp_'.$cuepoint.'.png';
        $svgimg = $video_name . '_svgimg_'.$cuepoint.'.png';


        // $img_path = dirname(__FILE__); // /Library/WebServer/Documents/laravel_video_feedback_v2/app/controllers
        $img_path = dirname(dirname(dirname(__FILE__))).'/public/img/'.$video_name.'/';
        // $app_pos = strrpos($img_path, 'app');

        // $img_path = substr_replace($img_path, '', $app_pos) . 'public/img/' . $video_name. '/'; // /Library/WebServer/Documents/laravel_video_feedback_v2/

        // $file_exist = '';
        // foreach( array_diff( scandir( $img_path ), array('.', '..') ) as $file ){
        //     if ($file == $img_name){
        //         $file_exist = 'true';
        //         break;
        //     }else{
        //         $file_exist = 'false';
        //     }
        // }
        // if ($file_exist == 'false') {
            // unlink( $img_path. '/' .$file );
            $img = str_replace('data:image/png;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $data = base64_decode($img);
            $file_save = $img_path.$img_tmp;
            file_put_contents($file_save, $data);
            // if ($success) {
            //     return 'draw image saved';
            // }else{
            //     return 'Draw image filed';
            // }
            // chmod($file_save, 777);
            $cmd = '/usr/local/bin/convert '.$file_save.' -resize 440x247 '.$img_path.$svgimg.'; rm '.$file_save;
            shell_exec($cmd);
            // return $shell_response;

        // }
        //save the svg image path to the database 
        // $svg_id = Input::get('svg_id');
        // if (!empty($svg_id)){
        //     $svg = Svg::find($svg_id);
        //     $svg->svgimg = $video_name.'/'.$svgimg;
        //     $svg->save();
        // }
        // DB::table('svgs')->where('id', 514);        
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
            return $thumbnail = Thumbnail::where('video_id', $video)->get();
        
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
        $thumbnail = Thumbnail::find($id); // $thumbnail represents the thumbnail table as an obj 
        $thumbnail_explode = explode('/', $thumbnail->thumbnail); // [chimpped, chimpped_00.png]

        $img_path = dirname(__FILE__); // /Library/WebServer/Documents/laravel_video_feedback_v2/app/controllers
        $app_pos = strrpos($img_path, 'app');

        $img_path = substr_replace($img_path, '', $app_pos) . 'public/img/' . $thumbnail_explode[0]; // /Library/WebServer/Documents/laravel_video_feedback_v2/

        // $thumbnail =  Thumbnail::find($id);
        // return $thumbnail->thumbnail; // img/chimpped_thumbs/chimpped_00_01.png 
        // $explode_thubnail = explode('/', $thumbnail) // [img, chimpped_thumbs, chimpped_00_01.png]
        //                                             //  [   0,           1,          2]

        foreach( array_diff( scandir( $img_path ), array('.', '..') ) as $file ){
            if ($file == $thumbnail_explode[1]) {
                unlink( $img_path. '/' .$file );
            }
        }

        $thumbnail->delete();

	}

}

