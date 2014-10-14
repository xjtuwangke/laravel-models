<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14/10/15
 * Time: 05:45
 */

namespace Xjtuwangke\LaravelModels\Migration;

use Xjtuwangke\LaravelSeeder\BasicTableSeeder;

class TagTableSeeder extends BasicTableSeeder{

    protected $tables = [ 'Xjtuwangke\LaravelModels\Tags\TagModel' ];

    protected function assignTags( $items , $tags , $min = 2 , $max = 4 ){
        foreach( $items as $item ){
            $num = rand( $min , $max );
            $_tags = array_rand( $tags , $num );
            $_strings = [];
            foreach( $_tags as $key ){
                $_strings[] = $tags[$key];
            }
            $item->setTags( $_strings );
        }
    }

}