<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-8-7
 * Time: 17:48
 */

namespace Xjtuwangke\LaravelModels;

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Xjtuwangke\LaravelModels\Cache\BasicModelCacheTrait;
use Xjtuwangke\LaravelModels\Observer\HistoryResourceTrait;

use Xjtuwangke\LaravelModels\Observer\ModelObserver;

class BasicModel extends \Eloquent {

    use SoftDeletingTrait , HistoryResourceTrait;

    use BasicModelCacheTrait;

    protected $dates = ['deleted_at'];

    protected $guarded = [ 'id' ];

    protected static $withModelObserver = true;

    public static function boot(){
        parent::boot();
        if( true == static::$withModelObserver ){
            static::observe( new ModelObserver );
        }

        $class = get_called_class();
        $ref = new \ReflectionClass( $class );
        $methods = $ref->getMethods( \ReflectionMethod::IS_STATIC );
        foreach( $methods as $method ){
            if( preg_match( '/^_onBoot_.*/' , $method->name ) ){
                $name = $method->name;
                static::$name();
            }
        }
    }

    public static function _schema( \Illuminate\Database\Schema\Blueprint $table ){
        $class = get_called_class();
        $ref = new \ReflectionClass( $class );
        $methods = $ref->getMethods( \ReflectionMethod::IS_STATIC );
        $table->engine = 'InnoDB';
        $table->increments( 'id' );
        foreach( $methods as $method ){
            if( preg_match( '/^_schema_.*/' , $method->name ) ){
                $name = $method->name;
                $table = static::$name( $table );
            }
        }
        $table->softDeletes();
        $table->timestamps();
        return $table;
    }

    public static function getTableName(){
        $model = new static;
        return $model->getTable();
    }

    public function getAttributeWithParents( $attribute ){
        return $this->$attribute;
    }

    public static function collectionToIdArray( $collection ){
        $idArray = [];
        foreach( $collection as $one ){
            $idArray[] = $one->getKey();
        }
        return $idArray;
    }

    public static function countExists( $attribute , $value , $withTrashed = true ){
        if( $withTrashed ){
            return static::withTrashed()->where( $attribute , $value )->count();
        }
        else{
            return static::where( $attribute , $value )->count();
        }
    }

    public function queryTargetsByLinkModel( $targetClass , $linkClass , $foreignKeyOfThis , $foreignKeyOfTarget ){
        $link = new $linkClass;
        $target = new $targetClass;
        return $targetClass::select( [ "{$target->getTable()}.*" , "{$link->getTable()}.{$foreignKeyOfThis}" , "{$link->getTable()}.{$foreignKeyOfTarget}" ]  )
            ->join( $link->getTable() , function( $join ) use( $link , $target , $foreignKeyOfThis , $foreignKeyOfTarget ){
            $join->on( "{$target->getTable()}.{$target->getKeyName()}" , '=' , "{$link->getTable()}.{$foreignKeyOfTarget}" )
                ->where( "{$link->getTable()}.{$foreignKeyOfThis}" , '=' , $this->getKey() );
        });
    }

    public function queryLinksByTargetModel( $linkClass , $targetClass ,$foreignKeyOfThis , $foreignKeyOfTarget ){
        $link = new $linkClass;
        $target = new $targetClass;
        return $linkClass::select( [ "{$link->getTable()}.*" ]  )
            ->join( $target->getTable() , function( $join ) use( $link , $target , $foreignKeyOfThis , $foreignKeyOfTarget ){
                $join->on( "{$target->getTable()}.{$target->getKeyName()}" , '=' , "{$link->getTable()}.{$foreignKeyOfTarget}" )
                    ->where( "{$link->getTable()}.{$foreignKeyOfThis}" , '=' , $this->getKey() );
            });
    }

    public function saveWithOutTimestamps(){
        $timestamps = $this->timestamps;
        $this->timestamps = false;
        $this->save();
        $this->timestamps = $timestamps;
    }

} 