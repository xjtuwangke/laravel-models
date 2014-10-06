<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-8-17
 * Time: 17:55
 */

namespace Xjtuwangke\LaravelModels\Observer;

trait HistoryOperatorTrait {

    public function operatorType(){
        return $this->getMorphClass();
    }

    public function operatorGroup(){
        return $this->group;
    }

    public function histories(){
        return $this->morphMany( 'HistoryModel' , 'operator' );
    }

} 