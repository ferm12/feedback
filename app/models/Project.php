<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class Project extends Eloquent implements UserInterface, RemindableInterface {
	// The database table used by the model.
	// @var string
	// protected $table = 'users';
	
	public function video(){
		return this->hasOne('Video');
	}
	
	public function company(){
		return this->belongsTo('Company');
	}
}
