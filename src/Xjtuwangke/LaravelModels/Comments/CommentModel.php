<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-8-28
 * Time: 22:26
 */

namespace Xjtuwangke\LaravelModels\Comments;

use Xjtuwangke\LaravelModels\BasicModel;
use Xjtuwangke\LaravelModels\Traits\MultiStatusTrait;

class CommentModel extends BasicModel{

    public static $AllowedStatus = [ '可见' , '不可见' ];

    use MultiStatusTrait;

    protected $table = 'comments';

    public function user(){
        return $this->hasOne( 'Xjtuwangke\LaravelModels\Users\UserModel' , 'id' , 'user_id' );
    }

    public function commentable(){
        return $this->morphTo();
    }

    public static function createByUser( BasicModel $user , BasicModel $commentable , array $comment ){
        $comment['user_id'] = $user->id;
        $comment['commentable_type'] = $commentable->getMorphClass();
        $comment['commentable_id']   = $commentable->id;
        return static::create( $comment );
    }

    public function topicRoot(){
        $comentable = $this->comentable;
        $root = $this;
        while( $comentable ){
            $root = $comentable;
        }
        return $root;
    }

} 