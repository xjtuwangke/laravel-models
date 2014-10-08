<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-9-2
 * Time: 0:49
 */

namespace Xjtuwangke\LaravelModels;

use Illuminate\Support\Facades\URL;
use Xjtuwangke\LaravelModels\Traits\ActivityTrait;

class HTMLize {

    protected $item = null;

    protected static $modelToAction = array();

    public static function registerActionForModel( $className , $action ){
        static::$modelToAction[ $className ] = $action;
    }

    public static function getUrl( $item ){
        if( $item ){
            $instance = HTMLize::create( $item );
            return $instance->url();
        }
        else{
            return 'javascript:;';
        }
    }

    public static function createDummy( $count = 1 ){
        if( $count <= 1 ){
            $dummy = new BasicModel();
            return static::create( $dummy );
        }
        else{
            $results = [];
            for( $i = 0 ; $i < $count ; $i++ ){
                $results[] = static::createDummy( 1 );
            }
            return $results;
        }
    }

    public static function createFromArray( $items ){
        $results = [];
        foreach( $items as $item ){
            $results[] = HTMLize::create( $item );
        }
        return $results;
    }

    public static function create( BasicModel $item = null ){
        $object = new HTMLize();
        return $object->setItem( $item );
    }

    public function setItem( $item ){
        $this->item = $item;
        return $this;
    }

    public function item(){
        return $this->item;
    }

    public function url(){
        if( ! $this->item ){
            return 'javascript:;';
        }
        $class = get_class( $this->item );
        if( ! array_key_exists( $class , static::$modelToAction ) ){
            return 'javascript:;';
        }
        else{
            $dict = static::$modelToAction[ $class ];
            if( ! is_callable( $dict ) ){
                return URL::action( $dict , [ $this->item->id ] );
            }
            else{
                return $dict( $this->item );
            }
        }
    }

    public static function imageFilter( $src , $action = null ){
        if( ! $src || 0 == strlen( trim( $src ) ) ){
            //$src = URL::asset('upload/default/no-image.jpeg');
        }
        return $src;
    }

    public function image(){
        if( ! $this->item ){
            //return URL::upload('default/no-image.jpeg');
        }
        if( method_exists( $this->item , 'getImage' ) ){
            return $this->item->getImage();
        }
        elseif( $this->item->image ){
            return $this->item->image;
        }
        elseif( $this->item->avatar ){
            return $this->item->avatar;
        }
        elseif( $this->item->profile ){
            if( $this->item->profile->avatar ){
                return $this->item->profile->avatar;
            }
            else{
                return '';
                //return KUrl::asset( 'images/img41.png' );
            }
        }
        else{
            return '';
            //return  KUrl::upload('default/no-image.jpeg');
        }
    }

    public function images( $min = 1 , $max = 10 ){
        if( ! $this->item ){
            return [];
            //return [ KUrl::upload('default/no-image.jpeg') ];
        }
        if( method_exists( $this->item , 'getImagesArray' ) ){
            $images = $this->item->getImagesArray();
        }
        else{
            return [];
            //$images = [ KUrl::upload('default/no-image.jpeg') ];
        }
        $images = array_slice( $images , 0 , $max );
        $more = $min - count( $images );
        for( $i = 0 ; $i < $more ; $i++ ){
            $images[] = '';
            //$images[] = KUrl::upload('default/no-image.jpeg');
        }
        return $images;
    }

    public function links(){
        if( method_exists( $this->item() , 'getLinks' ) ){
            return $this->item()->getLinks();
        }
        else{
            return array();
        }
    }

    public function title( $length = 30 ){
        if( ! $this->item ){
            return '';
        }
        if( $this->item->title ){
            $title = $this->item->title;
        }
        elseif( $this->item->name ){
            $title =  $this->item->name;
        }
        elseif( $this->item->activity_title ){
            $title =  $this->item->activity_title;
        }
        else{
            $title =  '名字去哪儿了';
        }
        return e( static::substr( $title , $length ) );
    }

    public function name( $length = 30 ){
        if( ! $this->item ){
            return '';
        }
        if( $this->item->username ){
            $name = $this->item->username;
        }
        else{
            $name = $this->title( $length );
        }
        return e( static::substr( $name , $length ) );
    }

    public function activityStatus( $flag_coming = '未开始' , $flag_during = '进行中', $flag_finished = '已结束', $flag_unknown = '' ){
        if( method_exists( $this->item , 'checkActivity' ) ){
            $status = $this->item->checkActivity();
            switch( $status ){
                case ActivityTrait::$During:
                    return $flag_during;
                case ActivityTrait::$Finished:
                    return $flag_finished;
                case ActivityTrait::$Not_Started:
                    return $flag_coming;
                default:
                    return $flag_unknown;
            }
        }
        else{
            return $flag_unknown;
        }
    }

    public function desc( $length = 300 ){
        if( ! $this->item ){
            return '';
        }
        if( $this->item->desc ){
            $text = $this->item->desc;
        }
        elseif( $this->item->intro ){
            $text =  $this->item->intro;
        }
        else{
            $text =  '暂无简介';
        }
        return e( static::substr( $text , $length ) );
    }

    static public function substr( $string , $length , $sufix = '...' ){
        if( mb_strlen( $string ) > $length ){
            $string = mb_substr( $string , 0 , $length ) . $sufix ;
        }
        return $string;
    }

    static public function br( $text ){
        $sentences = preg_split( '/(\n+)/' , $text );
        $result = '';
        foreach( $sentences as $sentence ){
            $result.= e( $sentence ) . '<br/>';
        }
        return $result;
    }

    public function __get( $name ){
        if( ! $this->item ){
            return '';
        }
        return $this->item->$name;
    }

    public function html5_intro( $default = '' ){
        if( ! $this->item ){
            return '';
        }
        elseif( $this->item->html5_intro ){
            return $this->item->html5_intro;
        }
        else{
            return $default;
        }
    }

}