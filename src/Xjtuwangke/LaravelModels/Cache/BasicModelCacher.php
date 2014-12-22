<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14/12/23
 * Time: 01:59
 */

namespace Xjtuwangke\LaravelModels\Cache;


use Xjtuwangke\LaravelModels\BasicModel;

class BasicModelCacher {

    public static function cacheTags( BasicModel $model ){
        $tags = array( 'QueryCache' , $model->getTableName() );
        if( $model->cache_tags ){
            $tags = array_merge( $tags , $model->cache_tags );
        }
        return $tags;
    }

    public static function prepare( BasicModel $model ){
        return \Cache::tags( static::cacheTags( $model ) );
    }

    public static function read( BasicModel $model , $sql ){
        if( $model->cache_enable ){
            return static::prepare( $model )->get( sha1( $sql ) );
        }
    }

    public static function write( BasicModel $model , $sql , $result ){
        if( $model->cache_enable ){
            static::prepare( $model )->put( sha1( $sql ) , $result , $model->cache_minutes );
        }
    }

    public static function flush( BasicModel $model ){
        static::prepare( $model )->flush();
    }
}