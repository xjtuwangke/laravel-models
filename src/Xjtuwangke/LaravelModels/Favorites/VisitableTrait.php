<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14/10/9
 * Time: 04:46
 */

namespace Xjtuwangke\LaravelModels\Favorites;


use Xjtuwangke\LaravelModels\BasicModel;

trait VisitableTrait {

    static public function _schema_visitableTrait( \Illuminate\Database\Schema\Blueprint $table ){
        $table->integer( 'visits' )->default( 0 );
        $table->integer( 'visits_real' )->default( 0 );
        return $table;
    }

    static protected function _onBoot_visitableTrait(){
        static::creating( function( $item ){
            $item->visits = rand( 12 , 24 );
        });
    }

    public function addVisit( BasicModel $user ){
        if( VisitModel::hasRelationShip( $user , $this ) ){
            return $this;
        };
        VisitModel::add( $user , $this );
        $this->visits+= 1;
        $this->visits_real+= 1;
        $this->save();
        return $this;
    }

    public function minusVisit( BasicModel $user ){
        if( VisitModel::hasRelationShip( $user , $this ) ){
            VisitModel::remove( $user , $this );
            if( $this->visits > 0 ){
                $this->visits-= 1;
            }
            if( $this->visits_real > 0 ){
                $this->visits_real-= 1;
            }
            $this->save();
            return $this;
        };
        return $this;
    }

}