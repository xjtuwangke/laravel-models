<?php

namespace Xjtuwangke\LaravelModels\Tags;

class TagModel extends BasicModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tags';

    public static function _schema_tagModel( \Illuminate\Database\Schema\Blueprint $table ){
        $table->text( 'name' )->nullable();
        $table->string( 'type' )->default( 'default' );
        $table->morphs( 'taggable' );
        return $table;
    }

    public function scopeOfModel( $query , $model ){
        return $query->where( 'tags.taggable_type' , $model );
        //User::ofGroup('admin')->get()
    }

    public function scopeOfType( $query , $type ){
        return $query->where( 'type' , $type );
    }

    public function taggable(){
        return $this->morphTo();
    }

    public static function tagNames( $model = null , $search = null , $type = 'default' , $limit = null ){
        $query = static::select( DB::raw( 'name , count(name) as count' ) )->groupBy( 'name' )->orderBy('count' , 'desc');
        if( $model ){
            $query = $query->ofModel( $model );
        }
        $query = $query->ofType( $type );
        if( $search ){
            $query = $query->where( 'name' , 'like' , "%{$search}%" );
        }
        if( $limit ){
            $query = $query->take( $limit );
        }
        $names = $query->get();
        $results = [];
        foreach( $names as $one ){
            $results[] = $one->name;
        }
        return $results;
    }

    public static function createTag( $item , $tag , $type = 'default' ){
        $class = $item->getMorphClass();
        $tag = static::firstOrCreate( array(
            'taggable_type' => $class ,
            'taggable_id'   => $item->id ,
            'name'          => $tag ,
            'type'          => $type ,
        ));
        return $tag;
    }

    public static function removeTag( $item , $tag = null , $type = 'defalut' ){
        $class = $item->getMorphClass();
        $id = $item->id;
        $where = array(
            'taggable_type' => $class ,
            'taggable_id'   => $id ,
        );
        if( null !== $tag ){
            $where['name'] = $tag;
        }
        if( null !== $tag ){
            $where['type'] = $type;
        }
        return static::where( $where )->delete();
    }

    public static function setTags( $item , $tags , $type = 'defalut' ){
        if( ! $tags ){
            $tags = [];
        }
        $old = $item->tagNameArray( $type );

        foreach( $old as $one ){
            if( ! in_array( $one , $tags ) ){
                static::removeTag( $item , $one , $type );
            }
        }
        foreach( $tags as $one ){
            if( ! in_array( $one , $old ) && strlen( trim( $one ) ) > 0 ){
                static::createTag( $item , $one , $type );
            }
        }
        return $item->tagNameArray( $type );
    }

}
