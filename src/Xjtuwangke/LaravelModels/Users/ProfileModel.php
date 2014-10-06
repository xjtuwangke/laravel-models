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

    public function user(){
        return $this->belongsTo('User' , 'user_id' , 'id');
    }

}
