<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14/12/23
 * Time: 02:07
 */

namespace Xjtuwangke\LaravelModels\Cache;


trait BasicModelCacheTrait {

    public $cache_enable = false;

    public $cache_minutes = 10;

    public $cache_tags = null;

    public static function _onBoot_modelCacher(){
        static::saved( function( $model ){
            BasicModelCacher::flush( $model );
        } );
        static::deleted( function( $model ){
            BasicModelCacher::flush( $model );
        } );
        static::restored( function( $model ){
            BasicModelCacher::flush( $model );
        } );
    }

    /**
     * Get a new query builder instance for the connection.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function newBaseQueryBuilder()
    {
        $builder = parent::newBaseQueryBuilder();
        if( $this->cache_enable ){
            $builder->cacheTags( BasicModelCacher::cacheTags( $this ) )->remember( $this->cache_minutes );
        }
        return $builder;
    }
}