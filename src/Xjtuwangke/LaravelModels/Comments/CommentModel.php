<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-8-28
 * Time: 22:26
 */

namespace Xjtuwangke\LaravelModels\Comments;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Xjtuwangke\LaravelModels\BasicModel;
use Xjtuwangke\LaravelModels\Traits\MultiStatusTrait;

class CommentModel extends BasicModel{

    public static $AllowedStatus = [ '可见' , '不可见' ];

    use MultiStatusTrait;

    protected $table = 'comments';

    public static function _schema( Blueprint $table ){
        $table = parent::_schema( $table );
        $table->integer( 'user_id' );
        $table->morphs( 'commentable' );
        $table->longText( 'comment' )->nullable();
    }

    public function user(){
        return $this->hasOne( 'Xjtuwangke\LaravelModels\Users\UserModel' , 'id' , 'user_id' );
    }

    public function commentable(){
        return $this->morphTo();
    }

    public static function createByUser( BasicModel $user , $commentable , array $comment ){
        $comment['user_id'] = $user->id;
        if( $commentable ){
            $comment['commentable_type'] = $commentable->getMorphClass();
            $comment['commentable_id']   = $commentable->id;
        }
        else{
            $comment['commentable_type'] = get_called_class();
            $comment['commentable_id']   = 0;
        }
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

    public static function lastActiveUsers( $take , $since = '1970-01-01 00:00:00' ){
        return static::select( DB::raw('count(*) as count, user_id') )
            ->where( 'status' , '可见' )
            ->where( 'created_at' , '>=' , $since )
            ->orderBy( 'created_at' , 'desc' )
            ->groupBy('user_id')
            ->take( $take )
            ->get();
    }

    public static function countUserComments( BasicModel $user , $since = '1970-01-01 00:00:00' ){
        return static::select( DB::raw('count(*) as count, user_id') )
            ->where( 'status' , '可见' )
            ->where( 'user_id' , $user->getKey() )
            ->where( 'created_at' , '>=' , $since )
            ->count();
    }

} 