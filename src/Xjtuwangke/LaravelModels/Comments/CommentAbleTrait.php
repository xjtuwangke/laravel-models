<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-8-28
 * Time: 22:32
 */

namespace Xjtuwangke\LaravelModels\Comments;

trait CommentAbleTrait {

    public function comments(){
        return $this->morphMany( 'CommentModel' , 'commentable' )->orderBy( 'created_at' , 'desc' );
    }
} 