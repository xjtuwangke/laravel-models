<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-9-7
 * Time: 20:39
 */

namespace Xjtuwangke\LaravelModels\Traits;

trait LikeableTrait {

    static public function _schema_likeableTrait( \Illuminate\Database\Schema\Blueprint $table ){
        $table->integer( 'likes' )->default( 0 );
        $table->integer( 'likes_real' )->default( 12 );
        $table->integer( 'visits' )->default( 0 );
        $table->integer( 'visits_real' )->default( 12 );
        return $table;
    }

    public function addLike(){
        $this->likes = $this->likes + 1;
        $this->likes_real = $this->likes_real + 1;
        $this->save();
        return $this;
    }

    public function addVisit(){
        $this->visits = $this->visits + 1;
        $this->visits_real = $this->visits_real + 1;
        $this->save();
        return $this;
    }

}