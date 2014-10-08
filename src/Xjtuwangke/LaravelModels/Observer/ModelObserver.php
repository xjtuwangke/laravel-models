<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-8-16
 * Time: 16:57
 */

namespace Xjtuwangke\LaravelModels\Observer;

use Xjtuwangke\LaravelModels\AuthModel;

class ModelObserver {

    static protected $operator = null;

    static public function setOperator( $operator ){
        static::$operator = $operator;
    }

    static public function getOperator(){
        return static::$operator;
    }

    protected function operator(){
        if( null == static::getOperator() ){
            return AuthModel::user();
        }
        else{
            return static::getOperator();
        }
    }

    public function saving( $model ){
        $operator = $this->operator();
    }

    public function saved( $model ){
        $operator = $this->operator();
    }

    public function creating( $model ){
        $operator = $this->operator();
    }

    public function created( $model ){
        $operator = $this->operator();
        HistoryModel::recordCreateAction( $operator , $model );
        return $model;
    }


    public function updating( $model ){
        $operator = $this->operator();
    }

    public function updated( $model ){
        $operator = $this->operator();
        HistoryModel::recordUpdateAction( $operator , $model );
        return $model;
    }

    public function deleting( $model ){
        $operator = $this->operator();
    }

    public function deleted( $model ){
        $operator = $this->operator();
        HistoryModel::recordDeleteAction( $operator , $model );
        return $model;
    }

    public function restoring( $model ){
        $operator = $this->operator();
    }

    public function restored( $model ){
        $operator = $this->operator();
        HistoryModel::recordRestoreAction( $operator , $model );
        return $model;
    }

} 