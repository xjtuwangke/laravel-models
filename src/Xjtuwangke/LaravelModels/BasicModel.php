<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-8-7
 * Time: 17:48
 */

namespace Xjtuwangke\LaravelModels;

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Xjtuwangke\LaravelModels\Observer\HistoryResourceTrait;

use Xjtuwangke\LaravelModels\Observer\ModelObserver;

class BasicModel extends \Eloquent {

    use SoftDeletingTrait , HistoryResourceTrait;

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

} 