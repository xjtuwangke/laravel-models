<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-8-16
 * Time: 18:12
 */

namespace Xjtuwangke\LaravelModels\Observer;

class HistoryModel extends \Eloquent {

    protected $table = 'histories';

    protected $guarded = ['id'];

    static public function _operator(  $operator , $model ){
        if( null == $operator ){
            $type = 'AdminUserModel';
            $id = 1;
        }
        else{
            $type = $operator->operatorType();
            $id = $operator->id;
        }
        return array( 'operator_type' => $type , 'operator_id' => $id , 'table' => $model->getTable() , 'resource_type' => $model->getMorphClass() , 'resource_id' => $model->id );
    }

    static public function recordCreateAction( $operator , $model ){
        $data = static::_operator( $operator , $model );
        $data['new'] = $model->toJson();
        $data['action'] = 'create';
        static::create( $data );
    }

    static public function recordUpdateAction( $operator , $model ){
        $data = static::_operator( $operator , $model );
        $original = [];
        $dirty = $model->getDirty();
        foreach( $dirty as $key => $val ){
            $original[$key] = $model->getOriginal( $key );
        }
        $data['old'] = json_encode( $original );
        $data['new'] = json_encode( $dirty );
        $data['action'] = 'update';
        static::create( $data );
    }

    static public function recordDeleteAction( $operator , $model ){
        $data = static::_operator( $operator , $model );
        $data['action'] = 'delete';
        $data['old'] = $model->toJson();
        static::create( $data );
    }

    static public function recordRestoreAction( $operator , $model ){
        $data = static::_operator( $operator , $model );
        $data['action'] = 'restore';
        $data['old'] = $model->toJson();
        static::create( $data );
    }

    static public function getRecordsByResource( $item , $limit = 200 ){
        $pattern = array(
            'table'
        );
    }

    public function resource(){
        return $this->morphTo();
    }

    public function operator(){
        return $this->morphTo();
    }

    static public function unserialize( $data ){
        $data = json_decode( $data , true );
        if( ! $data ){
            return [];
        }
        else{
            return $data;
        }
    }



} 