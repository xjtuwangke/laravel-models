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

    public static function queryM( BasicModel $item_n ){
        $attributes = array(
            static::$nameN . '_type' => $item_n->getMorphClass() ,
            static::$nameN . '_id'   => $item_n->getKey() ,
        );
        return static::where( $attributes );
    }

    public static function getM( BasicModel $item_n , $idArray = false ){
        if( ! $idArray ){
            return static::queryM( $item_n )->get();
        }
        else{
            $idArray = [];
            $collection = static::queryM( $item_n )->get();
            $idName = static::$nameM . '_id';
            foreach( $collection as $one ){
                $idArray[] = $one->{$idName};
            }
            return $idArray;
        }
    }

    public function itemM(){
        return $this->morphTo( static::$nameM );
    }

    public static function queryN( BasicModel $item_m ){
        $attributes = array(
            static::$nameM . '_type' => $item_m->getMorphClass() ,
            static::$nameM . '_id'   => $item_m->getKey() ,
        );
        return static::where( $attributes );
    }

    public static function getN( BasicModel $item_m , $idArray = false ){
        if( ! $idArray ){
            return static::queryN( $item_m )->get();
        }
        else{
            $idArray = [];
            $collection = static::queryN( $item_m )->get();
            $idName = static::$nameN . '_id';
            foreach( $collection as $one ){
                $idArray[] = $one->{$idName};
            }
            return $idArray;
        }
    }

    public function itemN(){
        return $this->morphTo( static::$nameN );
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

    public static function queryRelatedItemM( BasicModel $item_n , BasicModel $item_m ){
        $mTableName = $item_m->getTable();
        $mClass = get_class( $item_m );
        $lTableName = static::getTableName();
        return $mClass::select( $mTableName . '.*' )->join( $lTableName , function( $join )use( $item_n , $item_m , $mTableName , $lTableName ){
            $m = static::$nameM;
            $n = static::$nameN;
            $join->on( "{$lTableName}.{$m}_id" , "=" , "{$mTableName}." . $item_m->getKeyName() )
                ->where( "{$lTableName}.{$m}_type" , "=" , $item_m->getMorphClass() )
                ->where( "{$lTableName}.{$n}_type" , "=" , $item_n->getMorphClass() )
                ->where( "{$lTableName}.{$n}_id" , "=" , $item_n->getKey() );
        });
    }

    public static function queryRelatedItemN( BasicModel $item_m , BasicModel $item_n ){
        $nTableName = $item_n->getTable();
        $nClass = get_class( $item_n );
        $lTableName = static::getTableName();
        return $nClass::select( $nTableName . '.*' )->join( $lTableName , function( $join )use( $item_n , $item_m , $nTableName , $lTableName ){
            $m = static::$nameM;
            $n = static::$nameN;
            $join->on( "{$lTableName}.{$n}_id" , "=" , "{$nTableName}." . $item_n->getKeyName() )
                ->where( "{$lTableName}.{$n}_type" , "=" , $item_n->getMorphClass() )
                ->where( "{$lTableName}.{$m}_type" , "=" , $item_m->getMorphClass() )
                ->where( "{$lTableName}.{$m}_id" , "=" , $item_m->getKey() );
        });
    }

}