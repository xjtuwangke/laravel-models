<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14/10/9
 * Time: 04:23
 */

namespace Xjtuwangke\LaravelModels\Relationships;


use Illuminate\Database\Schema\Blueprint;
use Xjtuwangke\LaravelModels\BasicModel;

class MtoNRelationShip extends BasicModel{

    protected $table = 'm_to_n_relationship';

    protected static $nameM = 'm';

    protected static $nameN = 'n';

    public static function _schema( Blueprint $table ){
        $table = parent::_schema( $table );
        $table->morphs( static::$nameM );
        $table->morphs( static::$nameN );
        return $table;
    }

    public static function add( BasicModel $item_m , BasicModel $item_n ){
        $attributes = array(
            static::$nameM . '_type' => $item_m->getMorphClass() ,
            static::$nameM . '_id'   => $item_m->getKey() ,
            static::$nameN . '_type' => $item_n->getMorphClass() ,
            static::$nameN . '_id'   => $item_n->getKey() ,
        );
        $record = static::withTrashed()->where( $attributes )->first();
        if( $record ){
            if( $record->trashed() ){
                $record->restore();
            }
        }
        else{
            $record = static::create( $attributes );
        }
        return $record;
    }

    public static function remove( BasicModel $item_m , BasicModel $item_n ){
        $attributes = array(
            static::$nameM . '_type' => $item_m->getMorphClass() ,
            static::$nameM . '_id'   => $item_m->getKey() ,
            static::$nameN . '_type' => $item_n->getMorphClass() ,
            static::$nameN . '_id'   => $item_n->getKey() ,
        );
        return static::where( $attributes )->delete();

    }

    public static function hasRelationShip( BasicModel $item_m , BasicModel $item_n ){
        $attributes = array(
            static::$nameM . '_type' => $item_m->getMorphClass() ,
            static::$nameM . '_id'   => $item_m->getKey() ,
            static::$nameN . '_type' => $item_n->getMorphClass() ,
            static::$nameN . '_id'   => $item_n->getKey() ,
        );
        return static::where( $attributes )->first();
    }

    public static function getM( BasicModel $item_n ){
        $attributes = array(
            static::$nameN . '_type' => $item_n->getMorphClass() ,
            static::$nameN . '_id'   => $item_n->getKey() ,
        );
        return static::where( $attributes )->get();
    }

    public static function getN( BasicModel $item_m ){
        $attributes = array(
            static::$nameM . '_type' => $item_m->getMorphClass() ,
            static::$nameM . '_id'   => $item_m->getKey() ,
        );
        return static::where( $attributes )->get();
    }

    public static function setM( BasicModel $item_n , $items ){
        $attributes = array(
            static::$nameN . '_type' => $item_n->getMorphClass() ,
            static::$nameN . '_id'   => $item_n->getKey() ,
        );
        static::where( $attributes )->delete();
        $results = array();
        foreach( $items as $item ){
            $results[] = static::add( $item , $item_n );
        }
        return $results;
    }

    public static function setN( BasicModel $item_m , $items ){
        $attributes = array(
            static::$nameM . '_type' => $item_m->getMorphClass() ,
            static::$nameM . '_id'   => $item_m->getKey() ,
        );
        static::where( $attributes )->delete();
        $results = array();
        foreach( $items as $item ){
            $results[] = static::add( $item_m , $item );
        }
        return $results;
    }

}