<?php

namespace Xjtuwangke\LaravelModels\Rbac;

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

use Xjtuwangke\LaravelModels\BasicModel;
use Xjtuwangke\LaravelModels\Traits\SwitchableTrait;
use Xjtuwangke\LaravelModels\Observer\HistoryOperatorTrait;


class AdminUserModel extends BasicModel implements UserInterface, RemindableInterface{

	use UserTrait, RemindableTrait , UserRoleTrait , SwitchableTrait , HistoryOperatorTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'admins';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');

    protected $fillable = array('username' , 'mobile' , 'email');


}
