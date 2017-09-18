<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class Source extends Eloquent implements UserInterface, RemindableInterface {
	// The database table used by the model.
	// @var string
	// protected $table = 'users';

	public function version(){
		return this->belongsTo('Version');
	}
}
