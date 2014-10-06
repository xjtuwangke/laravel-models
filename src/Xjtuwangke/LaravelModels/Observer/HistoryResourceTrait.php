<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-8-17
 * Time: 17:39
 */

namespace Xjtuwangke\LaravelModels\Observer;

trait HistoryResourceTrait {

    public function histories(){
        return $this->morphMany( 'HistoryModel' , 'resource' );
    }

} 