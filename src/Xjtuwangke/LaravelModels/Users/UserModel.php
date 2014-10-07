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

    static public function _schema_usermodel( \Illuminate\Database\Schema\Blueprint $table ){
        $table->engine = 'InnoDB';
        $table->increments( 'id' );
        $table->string( 'username' , 100 );
        $table->string( 'nickname' , 30)->unique();
        $table->string( 'email' , 100 );
        $table->string( 'mobile' , 20 );
        $table->integer( 'exp' )->default( 0 );
        $table->integer( 'points' )->default( 0 );
        $table->date( 'birthdate' )->nullable();
        $table->enum( 'gender' , [ '男' , '女' , '保密' ] )->default( '保密' );
        $table->string( 'password' , 100 );
        $table->string( 'remember_token' , 100 );
        $table->timestamp( 'last_login' );
        $table->softDeletes();
        $table->timestamps();
        return $table;
    }

    public function scopeOfGroup( $query , $group ){
        return $query->where( 'user.group' , $group );
    }

    public function profile(){
        return $this->hasOne('ProfileModel' , 'user_id' , 'id'); //foreign key, local key
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