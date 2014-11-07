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
        $table->morphs( 'comment_root' );
        $table->longText( 'comment' )->nullable();
    }

    public function user(){
        if( class_exists( '\UserModel' ) ){
            return $this->hasOne( '\UserModel' , 'id' , 'user_id' );
        }
        else{
            return $this->hasOne( 'Xjtuwangke\LaravelModels\Users\UserModel' , 'id' , 'user_id' );
        }
    }

    public function commentRoot(){
        return $this->morphTo();
    }

    public function commentable(){
        return $this->morphTo();
    }

    public static function createByUser( BasicModel $user , $commentable , array $comment ){
        $comment['user_id'] = $user->id;
        $comment['comment_root_id'] = 0;
        $comment['comment_root_type'] = get_called_class();
        if( $commentable ){
            $comment['commentable_type'] = $commentable->getMorphClass();
            $comment['commentable_id']   = $commentable->getKey();
            $comment = static::create( $comment );
            if( $commentable->commentRoot ){
                //commentable有comment_root时继承comment_root
                $root = $commentable->commentRoot;
            }
            else{
                //否则root是commentable
                $root = $commentable;
            }
        }
        else{
            $comment['commentable_type'] = get_called_class();
            $comment['commentable_id']   = 0;
            $comment = static::create( $comment );
            $root = $comment;
        }
        $comment->comment_root_id = $root->getKey();
        $comment->comment_root_type = $root->getMorphClass();
        $comment->save();
        return $comment;

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

    public static function queryRespondedToUserComments( BasicModel $user , $query = null ){
        $idArray = array();
        $comments = static::where( array(
            'user_id' => $user->getKey() ,
        ))->select( [ 'id' ] )->get();
        foreach( $comments as $comment ){
            $idArray[] = $comment->id;
        }
        if( $query ){
            return $query->whereIn( 'commentable_id' , $idArray )->where( 'commentable_type' , get_called_class() );
        }
        else{
            return static::whereIn( 'commentable_id' , $idArray )->where( 'commentable_type' , get_called_class() );
        }
    }

} 