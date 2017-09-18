<?php

// use Illuminate\Auth\UserInterface;
// use Illuminate\Auth\Reminders\RemindableInterface;

// class Cue_Point extends Eloquent implements UserInterface, RemindableInterface {

class Comment extends Eloquent {

	// protected $guarded = array('id');
	// protected $fillable = array('');
	// public static $rules = array();

	// The database table used by the model.
    protected $table = 'Comments';

    protected $fillable = array('parent_id', 'cuepoint', 'cuepoint_seconds', 'comment', 'thumbnail', 'svg', 'video_id', 'user','attachments');

	public $timestamps = true;


    // protected $fillable = array('cuepoint_seconds', 'cuepoint', 'video_id');
	// public $timestamps = true;
	// public function note(){
	// 	return $this->hasOne('Note');
	// }
    // public function svg(){
    //     return $this->hasOne('Svg');
    // }
    // public function thumbnail(){
    //     return $this->hasOne('Thumbnail');
    // }
	// public function version(){
	// 	return $this->belongsTo('Version');
	// }
}
