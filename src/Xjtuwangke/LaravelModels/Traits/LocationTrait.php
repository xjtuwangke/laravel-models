<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-8-28
 * Time: 19:26
 */

namespace Xjtuwangke\LaravelModels\Traits;

trait LocationTrait {

    static public function _schema_locationTrait( \Illuminate\Database\Schema\Blueprint $table ){
        $table->string( 'longitude' )->nullable();
        $table->string( 'latitude' )->nullable();
        return $table;
    }

    public function setLocation( $longitude , $latitude ){
        $this->longitude = $longitude;
        $this->latitude  = $latitude;
        $this->save();
        return $this;
    }

    public function getLocation(){
        return [ $this->longitude , $this->latitude ];
    }

    public function baiduMapUrl( $title = '这里' ){
        $title = urlencode( $title );
        $lat = $this->latitude;
        $lng = $this->longitude;
        //$location['location_url'] = "http://map.baidu.com/?latlng=$lat,$lng&title=%E5%9C%BA%E5%9C%B0%E4%BD%8D%E7%BD%AE&content=".$title."&autoOpen=true&l=";
        return "http://api.map.baidu.com/marker?location=$lat,$lng&title=%E5%9C%BA%E5%9C%B0%E4%BD%8D%E7%BD%AE&content=" . $title . "&output=html&src=rollong|gofarms";
    }

    public function getdistance( $that ) {
        //将角度转为狐度
        $radLat1 = deg2rad($this->latitude);
        $radLat2 = deg2rad($that->latitude);
        $radLng1 = deg2rad($this->longitude);
        $radLng2 = deg2rad($that->longitude);
        $a = $radLat1 - $radLat2; //两纬度之差,纬度<90
        $b = $radLng1 - $radLng2; //两经度之差纬度<180
        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137;
        return $s;
    }

}