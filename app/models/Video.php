<?php

// use Illuminate\Auth\UserInterface;
// use Illuminate\Auth\Reminders\RemindableInterface;
//
// class Video extends Eloquent implements UserInterface, RemindableInterface {
// 	// The database table used by the model.
// 	// @var string
// 	// protected $table = 'videos';
//
// 	public function version(){
// 		return this->hasMany('Version');
// 	}
// 	
// 	public function project(){
// 		return this->belongsTo('Project');
// 	}
// }
//
class Video extends Eloquent {
    /*
     * The dabase table used by the nodel
     * @var string
     */
    
	protected $table = 'videos';
	
	protected $fillable = array('video_title', 'duration', 'fps', 'width', 'height', 'description', 'video_dir', 'video_srcs', 'url', 'project_id', 'project_name', 'clients_ids', 'active', 'progress_file', 'pid_file', 'total_frames');


	public $timestamps = true;

	// public function svg(){
	// 	return this->hasOne('Svg');
	// }
	// 
	// public function cuepoint(){
	// 	return $this->belongsTo('Cuepoint');
	// }
}
