<?php

namespace Xjtuwangke\LaravelModels\Users;

use Xjtuwangke\LaravelModels\BasicModel;

class ProfileModel extends BasicModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_profile';

    static public function _schema_profilemodel( \Illuminate\Database\Schema\Blueprint $table ){
        $table->integer( 'user_id' )->unsigned();
        $table->text( 'avatar' );
        return $table;
    }

    public function user(){
        return $this->belongsTo('UserModel' , 'user_id' , 'id');
    }

}
