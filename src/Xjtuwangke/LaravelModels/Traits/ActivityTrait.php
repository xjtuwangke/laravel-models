<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-8-28
 * Time: 19:46
 */

namespace Xjtuwangke\LaravelModels\Traits;

trait ActivityTrait {

    public static $Not_Started = -1;

    public static $During = 0;

    public static $Finished = 1;

    static public function _schema_activityTrait( \Illuminate\Database\Schema\Blueprint $table ){
        $table->dateTime( 'activity_start' )->nullable();
        $table->dateTime( 'activity_end' )->nullable();
        $table->dateTime( 'signup_start' )->nullable();
        $table->dateTime( 'signup_end' )->nullable();
        $table->text( 'activity_title' )->nullable();
        $table->longText( 'activity_content' )->nullable();
        $table->morphs( 'owner' );
        return $table;
    }

    public function setActivityContent( $content ){
        $this->activity_content = $content;
        $this->save();
        return $this;
    }

    public function setActivityTitle( $title ){
        $this->activity_title = $title;
        $this->save();
        return $this;
    }

    public function activityDurationString( $format = "Y-m-d H:i:s" ,  $glue = '到' ){
        if( $this->activity_start ){
            $start = date( $format , strtotime( $this->activity_start) );
        }
        else{
            $start = '未定';
        }
        if( $this->activity_end ){
            $end = date( $format , strtotime( $this->activity_end) );
        }
        else{
            $end = '未定';
        }
        return $start . $glue  . $end;
    }

    public function signupDurationString( $format = "Y-m-d H:i:s" , $glue = '到' ){
        if( $this->signup_start ){
            $start = date( $format , strtotime( $this->signup_start) );
        }
        else{
            $start = '未定';
        }
        if( $this->signup_end ){
            $end = date( $format , strtotime( $this->signup_end) );
        }
        else{
            $end = '未定';
        }
        return $start . $glue . $end;
    }

    public function setDate( $start , $end ){
        $this->activity_start = $start;
        $this->activity_end   = $end;
        $this->save();
        return $this;
    }

    public function setSignUpDate( $from , $to ){
        $this->signup_start = $from;
        $this->signup_end = $to;
        $this->save();
        return $this;
    }

    public function checkSignUp( $datetime = null ){
        if( null == $datetime ){
            $datetime = new \Carbon\Carbon();
        }
        return $this->checkDatetimeStatus( $this->signup_start , $this->signup_end , $datetime );
    }

    public function checkActivity( $datetime = null ){
        if( null == $datetime ){
            $datetime = new \Carbon\Carbon();
        }
        return $this->checkDatetimeStatus( $this->activity_start , $this->activity_end , $datetime );
    }

    public function checkDatetimeStatus( $start , $end , $check ){
        if( $check < $start ){
            return static::$Not_Started;
        }
        if( $check > $end ){
            return static::$Finished;
        }
        return static::$During;
    }

    //从ArticleTraits Copy过来

    public function queryRelated( $type = null ){
        $query = $this->morphMany( 'ArticleLinkToModel' , 'article' );
        if( null !== $type ){
            $query->where( 'article_link_type' , $type );
        }
        return $query;
    }

    public function getRelated( $type = null ){
        $related = $this->queryRelated( $type )->get();
        $results = [];
        foreach( $related as $one ){
            $results [] = $one->articleLinks;
        }
        return $results;
    }

} 