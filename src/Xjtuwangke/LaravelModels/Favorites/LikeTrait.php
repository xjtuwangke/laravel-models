<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14/11/6
 * Time: 05:30
 */

namespace Xjtuwangke\LaravelModels\Favorites;


use Xjtuwangke\LaravelModels\BasicModel;

trait LikeTrait {

    static public function _schema_likeTrait( \Illuminate\Database\Schema\Blueprint $table ){
        $table->integer( 'likes' )->default( 0 );
        $table->integer( 'likes_real' )->default( 0 );
        return $table;
    }

    static protected function _onBoot_favoriteTrait(){
        static::creating( function( $item ){
            $item->likes = rand( 3 , 12 );
        });
    }

    public function getLikesAttribute( $value ){
        $value = ( int ) $value;
        if( $value < 0 ){
            $value = 0;
        }
        return (string) $value;
    }

    public function getLikesRealAttribute( $value ){
        $value = ( int ) $value;
        if( $value < 0 ){
            $value = 0;
        }
        return (string) $value;
    }

    public function isLikedByUser( $user ){
        if( ! $user ){
            return false;
        }
        if( LikeModel::hasRelationShip( $user , $this ) ){
            return true;
        }
        else{
            return false;
        }
    }

    public function addLike( BasicModel $user ){
        if( LikeModel::hasRelationShip( $user , $this ) ){
            return $this;
        };
        LikeModel::add( $user , $this );
        $this->likes+= 1;
        $this->likes_real+= 1;
        $this->save();
        return $this;
    }

    public function minusLike( BasicModel $user ){
        if( LikeModel::hasRelationShip( $user , $this ) ){
            LikeModel::remove( $user , $this );
            if( $this->likes > 0 ){
                $this->likes-= 1;
            }
            if( $this->likes_real > 0 ){
                $this->likes_real-= 1;
            }
            $this->save();
            return $this;
        };
        return $this;
    }
}