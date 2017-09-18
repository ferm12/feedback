<?php

class NotesController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return Note::all();	
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
     * store the note only and only if its cuepoint exits
     *
	 * @return Response
	 */
	public function store()
	{
		$note = new Note(array(
            'note'      => Input::get('note'),
            'video_id'  => Input::get('video_id'),
            'user'      => Input::get('user')
		));	

		$id = Input::get('cuepoint_id');
		$cuepoint = Cuepoint::find($id);
		$cuepoint_note = $cuepoint->note();

		return 	$cuepoint_note->save($note);
/*
		return Note::create(array(
			'note' => Input::get('note'),
			'cuepoint_id' => Input::get('cuepoint_id')
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
            return Note::find($id);
        else
            return Note::where('video_id', $video)->get();
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
		$note = Note::find($id);
		$note->note = Input::get('note');
		$note->save();
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Note::find($id)->delete();
    }
}
/*
CRUD maps to REST like:
create  → POST      /collection
read    → GET       /collection[/id]
update  → PUT       /collection/id
patch   → PATCH     /collection/id
delete  → DELETE    /collection/id
*/

