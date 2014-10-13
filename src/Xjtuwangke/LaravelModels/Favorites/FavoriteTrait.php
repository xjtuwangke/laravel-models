<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14/10/9
 * Time: 04:46
 */

namespace Xjtuwangke\LaravelModels\Favorites;


use Xjtuwangke\LaravelModels\BasicModel;

trait FavoriteTrait {

    static public function _schema_favoriteTrait( \Illuminate\Database\Schema\Blueprint $table ){
        $table->integer( 'favorites' )->default( 0 );
        $table->integer( 'favorites_real' )->default( 0 );
        return $table;
    }

    static protected function _onBoot_favoriteTrait(){
        static::creating( function( $item ){
            $item->favorites = rand( 3 , 12 );
        });
    }

    public function isFavoritedByUser( $user ){
        if( ! $user ){
            return false;
        }
        if( FavoriteModel::hasRelationShip( $user , $this ) ){
            return true;
        }
        else{
            return false;
        }
    }

    public function addFavorite( BasicModel $user ){
        if( FavoriteModel::hasRelationShip( $user , $this ) ){
            return $this;
        };
        FavoriteModel::add( $user , $this );
        $this->favorites+= 1;
        $this->favorites_real+= 1;
        $this->save();
        return $this;
    }

    public function minusFavorite( BasicModel $user ){
        if( FavoriteModel::hasRelationShip( $user , $this ) ){
            FavoriteModel::remove( $user , $this );
            if( $this->favorites > 0 ){
                $this->favorites-= 1;
            }
            if( $this->favorites_real > 0 ){
                $this->favorites_real-= 1;
            }
            $this->save();
            return $this;
        };
        return $this;
    }

}