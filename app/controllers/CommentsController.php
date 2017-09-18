<?php
// timezone need it for created_at and updated_at field in the database
date_default_timezone_set('America/Los_Angeles');

class CommentsController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return Comment::all();
		// return Comment::find(1);
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
        $cuepoint = Input::get('parentComment')['cuepoint'];
        if ( !empty($cuepoint) ){
            $video = Video::find( Input::get('parentComment')['video_id'] );
            $video_title = $video->video_title;
            // Format cupoint from MM:SS to MM_SS 
            $cuepoint_formatted = Input::get('parentComment')['cuepoint'];
            $cuepoint_formatted[strrpos($cuepoint_formatted,':')] = '_';
            // cuepoint in secons e.g 1.34
            $cuepoint_seconds = Input::get('parentComment')['cuepoint_seconds'];

            $app_root = dirname(dirname(dirname(__FILE__)));
            $video_dir = $app_root.'/public/video_review/'.$video_title.'/';

            // create video_dir if it does not exist
            if ( !is_dir( $video_dir ) )
                mkdir( $video_dir, 0777, true );

            // $cmd = 'ffmpegthumbnailer -i /Library/WebServer/Documents/laravel_video_feedback_v1/public/videos/chimpped.mp4 -o /Library/WebServer/Documents/laravel_video_feedback_v1/public/img/chimmped_test1.png -s 440 -t 00:00:50'
            // capture the exact video frame at $cuepoint_seconds as a png using ffmpeg 
            $cmd = '/usr/local/bin/ffmpeg -i '.$video_dir.$video_title.'.mp4 -ss '.$cuepoint_seconds.' -f image2 -vframes 1 -s 440x247 '.$video_dir.$video_title.'_frame_'.$cuepoint_formatted.'.png';
            $shell_response = shell_exec($cmd);
        }

        $comment = Comment::create( array(
            'parent_id'         => Input::get('parentComment')['parent_id'],
            'cuepoint'          => empty($cuepoint) ? null : Input::get('parentComment')['cuepoint'],
            'cuepoint_seconds'  => empty($cuepoint) ? null : Input::get('parentComment')['cuepoint_seconds'],
            'comment'           => Input::get('parentComment')['comment'],
            'thumbnail'         => empty($cuepoint) ? null : $video_title.'/'.$video_title.'_frame_'.$cuepoint_formatted.'.png',
            'svg'               => empty($cuepoint) ? null : Input::get('parentComment')['svg'],
            'video_id'          => Input::get('parentComment')['video_id'],
            'user'              => Input::get('parentComment')['user']
        ));
        // Convert obj to array
        $created_at = (array) $comment->created_at;
        $created_at = explode('.', $created_at['date']);
        $updated_at = (array) $comment->updated_at;
        $updated_at = explode('.', $updated_at['date']);
        
        return array('parentComment' =>
            array(
                'id'                => $comment->id,
                'parent_id'         => $comment->parent_id,
                'cuepoint'          => $comment->cuepoint,
                'cuepoint_seconds'  => $comment->cuepoint_seconds,
                'comment'           => $comment->comment,
                'thumbnail'         => $comment->thumbnail,
                'svg'               => $comment->svg,
                'video_id'          => $comment->video_id,
                'user'              => $comment->user,
                'created_at'        => $created_at[0],
                'update_at'         => $updated_at[0]
            )
        );
        // $created_at = (array) $comment->created_at;
        
        // $comment->toDateTimeString();
        // return var_dump($created_at);
        // json_encode($comment->created_at);

		// $id = DB::table('cuepoints')->insertGetId(array(
		// 	'cue_point' => Input::get('cue_point'),
		// 	'cue_point_code' => Input::get('cue_point_code')
		// ));

        // return $id;

		// return Comment::create( array(
            // 'cuepoint_seconds'  => Input::get('cuepoint_seconds'),
            // 'cuepoint'          => Input::get('cuepoint'),
            // 'video_id'          => Input::get('video_id'),
		// ));	
		
		// return Input::json()->all();
		// $jsonFeedback = Input::get('feedback.0');
		// $stringJsonFeedback = settype($jsonFeedback, "string");
		// $jsonDecoFeedback =json_decode($stringJsonFeedback);
		// return $jsonDecoFeedback;

		// if (Input::has('note'))
		// {
		// 	$returnString = 'true';
		// }else{
		// 	$returnString = 'false';
		// }
		// return Input::json()->all();
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show( $id, $video_id = null)
	{
        // if ($video == null)
        //    $comments = Comment::find($id);
        // else
        //     $comments = Comment::where('video_id', $video)->get();
        $comments = Comment::where('video_id', $video_id)->get();
        $comments_data = [];

        /**
         * Formats the $comments_data array recursively to indicate nested comments.
         *  [
         *      [
         *          'parentComment' => $comment,
         *          'childrenComments' => [
         *              [
         *                  'parentComment' => $comment,
         *                  'childrenComments => [
         *                      ...
         *                  ]
         *              ]
         *          ]
         *      ]
         *  ]
         * Notice that parentComment is a single $comment obj, while childrenComments is an array of arrays
         *
         * @param array $comments_iterable_array is passed by reference to indicate that we want to change $comments_data.
         * @param obj $comment 
         */
        function comments_data_iterator( &$comments_iterable_array, $comment){
            $n = 0;
            foreach ($comments_iterable_array as $comment_iterable_array){
                //if this is the parent add this children
                if ( $comment_iterable_array['parentComment']->id === $comment->parent_id ){
                    if ( !(isset($comment_iterable_array['childrenComments'])) ) 
                        $comments_iterable_array[$n]['childrenComments'] = array(array('parentComment' => $comment ));
                    else
                        array_push($comments_iterable_array[$n]['childrenComments'] , array('parentComment' => $comment ) );
                //if this is not the parent check if it has children and check there
                }else{
                    if ( (isset($comment_iterable_array['childrenComments'])) ) 
                        comments_data_iterator($comments_iterable_array[$n]['childrenComments'], $comment);
                }
                $n++;
            }
        }
         
       foreach ( $comments as $comment ) {
            //if the parent_id is empty the comment becomes the parentComment
            if ( empty( $comment->parent_id ) ){
                array_push( $comments_data,  array( 'parentComment' => $comment ) );
            //else, we look for the parent of the child in the. 
                // return print_r($comments_data);
            }else{
                comments_data_iterator( $comments_data, $comment );
            }
        }
        return json_encode($comments_data);
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
		$comment = Comment::find($id);

		$comment->comment   = Input::get('parentComment')['comment'];
        $comment->svg       = Input::get('parentComment')['svg'];
		$comment->save();
        return $comment;
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
    {  
        // checks to see if there are more than thumbail with the same name
        function checkForMorethanOne($id){
            $comment = Comment::find($id);
            $thumbnails = Comment::where('video_id', $comment->video_id)->get();
            $count = 0;
            foreach ( $thumbnails as $thumbnail ){
                if ( !empty($comment->thumbnail) && $thumbnail->thumbnail == $comment->thumbnail ){
                    $count++;
                }
            }
            return $count;
        }
        // only deletes the thumbnail if no other thumbnail depends on it. 
        function deleteFile($id){
            if ( checkForMorethanOne($id) == 1 ){
                $comment = Comment::find($id);
                $comment = explode("/", $comment->thumbnail);
                $app_root = dirname(dirname(dirname(__FILE__)));
                $video_dir = $app_root.'/public/video_review/'.$comment[0].'/';
                // Loop through the files
                foreach(array_diff(scandir($video_dir), array('.', '..')) as $file) {
                    if ($file == $comment[1])
                        //delete file
                        unlink($video_dir.$comment[1]);
                }
            } 
        }

        //if comment constains children it deletes them recursevely
        function destroyRecursive($id){
            deleteFile($id);
            
            Comment::find($id)->delete();
            $ids_to_delete_next = Comment::where('parent_id', $id)->get();
            foreach ($ids_to_delete_next as $id_to_delete_next){
                if(isset($id_to_delete_next->id))
                    destroyRecursive($id_to_delete_next->id);
            }
        }
        destroyRecursive($id);
	}

}
