<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-8-28
 * Time: 22:22
 */

namespace Xjtuwangke\LaravelModels\Traits;

trait OnAndOffScheduleTrait {

    static public function _schema_onAndOffScheduleTraitTrait( \Illuminate\Database\Schema\Blueprint $table ){
        $table->dateTime( 'on_time' )->nullable();
        $table->dateTime( 'off_time' )->nullable();
        return $table;
    }

    public function setOnTime( $datetime = null ){
        $this->on_time = $datetime;
        $this->save();
        return $this;
    }

    public function setOffTime( $datetime = null ){
        $this->off_time = $datetime;
        $this->save();
        return $this;
    }

} 