<?php

namespace Xjtuwangke\LaravelModels\Users;

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

use Xjtuwangke\LaravelModels\BasicModel;
use Xjtuwangke\LaravelModels\Traits\SwitchableTrait;
use Xjtuwangke\LaravelModels\Observer\HistoryOperatorTrait;

class UserModel extends BasicModel implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait , SwitchableTrait , HistoryOperatorTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');

    //protected $fillable = array('username' , 'mobile' , 'email');

    protected $morphClass = 'UserModel';

    public function scopeOfGroup( $query , $group ){
        return $query->where( 'user.group' , $group );
        //User::ofGroup('admin')->get()
    }

    public function profile(){
        return $this->hasOne('ProfileModel' , 'user_id' , 'id'); //foreign key, local key
    }

    public function addresses(){
        return $this->hasMany( 'AddressModel' , 'user_id' , 'id' )->orderBy( 'updated_at' , 'desc' )->take( 5 );
    }

    public function orders(){
        return $this->hasMany('OrderModel' , 'user_id' , 'id')->orderBy( 'created_at' , 'desc' );
    }

    public function avatar(){
        return $this->morphOne( 'ImageModel' , 'imageable' );
    }

    public function roles(){
        return $this->belongsToMany('Role' , 'user_roles' , 'user_id' , 'role_id');
    }

    static function generateNickname(){
        return 'user_' . time() . sprintf( '%08d' , rand( 0 , 99999999 ) );
    }

    static function createUser( $name , $password , $mobile = null , $email = null  , $nick = null ){
        if( ! $nick ){
            $nick = static::generateNickname();
        }
        $password = \Hash::make( $password );
        if( is_null( $mobile ) && is_null( $email ) ){
            return false;
        }

        $user = static::create( ['username' => $name , 'nickname' => $nick , 'mobile' => $mobile , 'email' => $email , 'password' => $password ] );
        $profile = ProfileModel::create( [ 'user_id' => $user->id ] );
        $profile->save();
        return $user;
    }

    public function changePassword( $password ){
        $this->password = \Hash::make( $password );
        $this->save();
        return $this;
    }

}
