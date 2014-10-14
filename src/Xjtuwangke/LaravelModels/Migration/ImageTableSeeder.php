<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14/10/15
 * Time: 05:46
 */

namespace Xjtuwangke\LaravelModels\Migration;

use Xjtuwangke\LaravelModels\Images\ImageModel;
use Xjtuwangke\LaravelSeeder\BasicTableSeeder;

class ImageTableSeeder extends BasicTableSeeder{

    protected $tables = [ 'Xjtuwangke\LaravelModels\Images\ImageModel' ];

    protected $doNotDelete = [ 'Xjtuwangke\LaravelModels\Images\ImageModel' ];

    protected function assignImage( $items ){
        foreach( $items as $item ){
            if( method_exists( $item , 'updateImage' ) ){
                $image = ImageModel::where( 'imageable_id' , '0' )->take( 80 )->get()->toArray();
                $image = $image[ array_rand( $image , 1 ) ];
                $item->updateImage( $image['url'] );
            }
            if( method_exists( $item , 'updateMultiImages' ) ){
                $images = ImageModel::where( 'imageable_id' , '0' )->take( 80 )->get()->toArray();
                $rands = array_rand( $images , 2 );
                $urls = [];
                foreach( $rands as $rand ){
                    $urls[] = $images[ $rand ]['url'];
                }
                $item->updateMultiImages( $urls );
                $this->command->info( 'updated image of ' . $item->getMorphClass() . '@' . $item->id );
            }

        }
    }

}