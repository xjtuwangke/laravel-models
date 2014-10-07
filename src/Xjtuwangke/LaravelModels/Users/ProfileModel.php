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
        $table->engine = 'InnoDB';
        $table->increments( 'id' );
        $table->integer( 'user_id' )->unsigned();
        $table->text( 'avatar' );
        $table->softDeletes();
        $table->timestamps();
        return $table;
    }

    public function user(){
        return $this->belongsTo('User' , 'user_id' , 'id');
    }

}
