<?php

class SvgsController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return Svg::all();
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
        $svg = new Svg(array(
            'svg'       => Input::get('svg'),
            'video_id'  => Input::get('video_id')
        ));
            
        $id = Input::get('cuepoint_id');
        $video_title = Input::get('video_title');
        $cuepoint = Cuepoint::find($id);
        $cuepoint_svg = $cuepoint->svg();

        $cuepoint_format = DB::select('select cuepoint from cuepoints where id = ?', array($id));
        $cuepoint_format = $cuepoint_format[0]->cuepoint;
        $cuepoint_format[strrpos($cuepoint_format,':')] = '_';
        $cuepoint_format =$cuepoint_format;

        //$app_root = dirname(dirname(dirname(__FILE__)));  // Library/WebServe/Documents/lavavel_video_feedback_v2 
        $svg_text = '<svg id="SvgjsSvg1000" xmlns="http://www.w3.org/2000/svg" version="1.1" width="640" height="360" xmlns:xlink="http://www.w3.org/1999/xlink">'.Input::get('svg').'</svg>';
        // makes a  svg file and converts the svg to png
        // $cmd1 ='mktemp /tmp/'.$cuepoint_format.'.svg; chmod 777 /tmp/'.$cuepoint_format.'.svg; SVG=\''.$svg_text.'\'; echo $SVG > /tmp/'.$cuepoint_format.'.svg; /usr/local/bin/convert -background none /tmp/'.$cuepoint_format.'.svg '.$app_root.'/public/img/'.$video_title.'/'.$video_title.'_tmp_'.$cuepoint_format.'.png; rm /tmp/'.$cuepoint_format.'.svg';
        // shell_exec($cmd1);

        // $cmd2 = '/usr/local/bin/convert '.$app_root.'/public/img/'.$video_title.'/'.$video_name.'_tmp_'.$cuepoint_format.'.png -resize 440x247 '.$app_root.'/public/img/'.$video_name.'/'.$video_name.'_draw_'.$cuepoint_format.'.png; rm '.$app_root.'/public/img/'.$video_name.'/'.$video_name.'_tmp_'.$cuepoint_format.'.png';
        // shell_exec($cmd2);
        // return $cmd2;
        // return $cmd1;
        return $cuepoint_svg->save($svg);
        
/*
        return Svg::create(array(
			'svg' => Input::get('svg'),
			'' => Input::get('')
        ));	
*/
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
            return Svg::find($id);
        else
            return Svg::where('video_id', $video)->get();
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
		$svg = Svg::find($id);
		$svg->svg = Input::get('svg');
		$svg->save();
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$task = Svg::find($id)->delete();
	}

}
