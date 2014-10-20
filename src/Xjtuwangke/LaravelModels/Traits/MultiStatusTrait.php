<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-8-14
 * Time: 20:47
 */

namespace Xjtuwangke\LaravelModels\Traits;

trait MultiStatusTrait {

    //$StatusDateTime

    static public function _schema_multiStatusTrait( \Illuminate\Database\Schema\Blueprint $table ){
        $status = static::$AllowedStatus;
        $default = $status[0];
        $table->enum( 'status' , $status )->default( $default );
        if( isset( static::$StatusDateTime ) ){
            foreach( static::$StatusDateTime as $column ){
                if( ! in_array( $column , [ 'created_at' , 'updated_at' , 'deleted_at' ] ) ){
                    $table->dateTime( $column )->nullable();
                }
            }
        }
        $table->index( [ 'status' ] );
        return $table;
    }

    public function scopeOfStatus( $query , $status ){
        return $query->where( 'status' , $status );
        // $users = User::ofStatus('member')->get();
    }

    public function changeStatus( $status ){
        $old = $this->status;
        $new = $status;
        if( ! isset( static::$AllowedStatusChange ) ){
            $this->status = $status;
        }
        else{
            $allowed = $this->getAvailableNextStatus();
            if( ! in_array( $status , $allowed ) ){
                return false;
            }
            $this->status = $status;
        }
        if( isset( static::$StatusDateTime ) && array_key_exists( $status , static::$StatusDateTime) ){
            $column = static::$StatusDateTime[$status];
            $this->$column = date('Y-m-d H:i:s');
        }
        $this->save();
        \Event::fire( get_class() . '.status.changed' , array( $this , $old , $new ) );
        return $this;
    }

    public function statusChangedAt( $status ){
        if( isset( static::$StatusDateTime ) && is_array( static::$StatusDateTime ) && array_key_exists( $status , static::$StatusDateTime ) ){
            $column = static::$StatusDateTime[$status];
            return $this->$column;
        }
        else{
            return null;
        }
    }

    public function getStatus(){
        return $this->status;
    }

    public function getAvailableNextStatus(){
        if( ! isset( static::$AllowedStatusChange ) ){
            return static::$AllowedStatus;
        }
        else{
            $allowed = [];
            foreach( static::$AllowedStatusChange as $one ){
                if( $one[0] == $this->status ){
                    $allowed[] = $one[1];
                }
            }
            return $allowed;
        }
    }

} 