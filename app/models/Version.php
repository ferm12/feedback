<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class Version extends Eloquent implements UserInterface, RemindableInterface {
	// The database table used by the model.
	// @var string
	// protected $table = 'versions';
	
	public function cue_point(){
		return this->hasMany('Cue_Point');
	}

	public function source(){
		return this->hasOne('Source');	
	}
}
