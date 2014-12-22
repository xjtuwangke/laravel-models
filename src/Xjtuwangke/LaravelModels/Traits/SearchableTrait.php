<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14/11/7
 * Time: 18:40
 */

namespace Xjtuwangke\LaravelModels\Traits;


trait SearchableTrait {

    public static function search( $field , $value ){
        $class = get_called_class();
        $ref = new \ReflectionClass( $class );
        $method = '_search_' . $field;
        if( $ref->hasMethod( $method ) && $ref->getMethod( $method ) ){
            static::$method( $value );
        }
    }
}