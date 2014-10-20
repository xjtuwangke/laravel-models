<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-8-14
 * Time: 20:42
 */

namespace Xjtuwangke\LaravelModels\Traits;

trait SwitchableTrait {

    static public function _schema_switchableTable( \Illuminate\Database\Schema\Blueprint $table ){
        $table->enum( 'switch' , [ '启用' , '禁用' ] );
        $table->index( [ 'switch' ] );
        return $table;
    }

    static public function queryAllAvailable(){
        return static::where( 'switch' , '启用' );
    }

    static public function getAllAvailable(){
        return static::queryAllAvailable()->get();
    }

    static public function queryAllUnavailable(){
        return static::where( 'switch' , '启用' );
    }

    static public function getAllUnavailable(){
        return static::queryAllUnavailable()->get();
    }

    public function switchOn(){
        $this->switch = '启用';
        $this->save();
        return $this;
    }

    public function switchOff(){
        $this->switch = '禁用';
        $this->save();
        return $this;
    }

    public function switchIsOn(){
        return $this->switch == '启用';
    }

    public function switchIsOff(){
        return $this->switch == '禁用';
    }

    public function switchToggle(){
        if( $this->switchIsOn() ){
            $this->switchOff();
        }
        else{
            $this->switchOn();
        }
        return $this;
    }

} 