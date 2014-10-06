<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-8-21
 * Time: 17:27
 */

namespace Xjtuwangke\LaravelModels\Tags;

trait TaggableTrait {

    public $modifiedTags = array();

    public static function _onBoot_taggableTrait(){
        static::saved(function($item){
            $tags = $item->modifiedTags;
            foreach( $tags as $type => $values ){
                TagModel::setTags( $item , $values , $type );
            }
            $item->modifiedTags = array();
            return $item;
        });
    }

    public function tags( $type = 'default' ){
        return $this->morphMany( 'TagModel' , 'taggable' )->ofType( $type );
    }

    public function getTagsByType( $type = 'default' ){
        return $this->morphMany( 'TagModel' , 'taggable' )->ofType( $type )->get();
    }

    public static function bindFormActionTags( $form , $item = null , $id = 0 ){
        if( ! isset( static::$tagTypes ) ){
            $types = [ 'default' => '标签' ];
        }
        else{
            $types = static::$tagTypes;
        }
        foreach( $types as $type => $lable ){
            $form->addField( FormFieldBase::createByType( 'tags_' . $type , FormFieldBase::Type_Tags )
                    ->setLabel( $lable )->setDefault( [] )
            );
            if( null != $item && 0 !== $id ){
                $form->setDefault( 'tags_' . $type , $item->tagNameArray( $type ) );
            }
            $form->setSaveFunc( 'tags_' . $type , function( $item , $form , $field ){
                $tags = $field->value();
                $name = $field->name();
                $type = substr( $name , 5 );
                //TagModel::setTags( $item , $tags , $type );
                $item->modifiedTags[ $type ] = $tags;
                return $item;
            });
        }
        return $form;
    }

    public function relatevedItemsByTag( $min = 3 , $max = 10 , $type = 'default' , $class = null ){
        $tags = $this->getTagsByType( $type );
        $this_id = $this->{$this->primaryKey};
        $tagNames = [];
        foreach( $tags as $tag ){
            $tagNames[] = $tag->name;
        }
        if( is_null( $class ) ){
            $class = $this->getMorphClass();
        }

        if( ! empty( $tagNames ) ){
            $id_collection = TagModel::select( 'taggable_id' )
                ->where( 'taggable_type' , $class )->where( 'type' , $type )->where( 'taggable_id' , '!=' , $this_id )
                ->whereIn( 'name' , $tagNames )->get();
            $idArray = [];
            foreach( $id_collection as $one ){
                $idArray[] = $one->taggable_id;
            }
            if( empty( $idArray ) ){
                $idArray = [ -1 ];
            }
            $items = $class::whereIn( $this->primaryKey ,  [ -1 ] )->take( $max )->get();
        }
        else{
            $idArray = [ -1 ];
            $items = $class::whereIn( $this->primaryKey , $idArray )->take( $max )->get();
        }
        $more = $min - $items->count();
        if( $more > 0 ){
            $more = $class::whereNotIn( $this->primaryKey , $idArray )->where( $this->primaryKey , '!=' , $this_id )->take( $more )->get();
            $items = $items->merge( $more );
        }
        return $items;
    }

    public function tagNameArray( $type = 'default' ){
        $tags = $this->getTagsByType( $type );
        $result = [];
        if( ! $tags ){
            return [];
        }
        else{
            foreach( $tags as $tag ){
                $result[] = $tag->name;
            }
        }
        return $result;
    }

    public function hasTag( $name , $type = 'default' ){
        $tags = $this->tagNameArray( $type );
        return in_array( $name , $tags );
    }

    public function addTag( $tag , $type = 'default' ){
        return TagModel::createTag( $this , $tag , $type );
    }

    public function removeTag( $tag = null , $type = 'default' ){
        return TagModel::removeTag( $this , $tag , $type );
    }

    public static function withTag( $tagName , $type = 'default' , $query = null ){
        $class = get_class();
        $class = new $class;
        $class = $class->getMorphClass();
        $tags = TagModel::where( 'taggable_type' , '=' , $class )->where( 'name' , '=' , $tagName );
        if( $type ){
            $tags = $tags->where( 'type' , '=' , $type );
        }
        $tags = $tags->get();
        $results = [];
        foreach( $tags as $one ){
            $results[] = $one->taggable_id;
        }
        if( is_null( $query ) ){
            $query = static::where( 'id' , '!=' , '-1' );
        }
        if( empty( $results ) ){
            return $query->where( 'id' , '=' , '-1' );
        }
        return $query->whereIn( 'id' , $results );
    }

    public static function withTags( array $tags ){
        $query = null;
        foreach( $tags as $tag ){
            if( ! is_array( $tag ) ){
                $name = $tag;
                $type = null;
            }
            else{
                $name = $tag[0];
                $type = $tag[1];
            }
            $query = static::withTag( $name , $type , $query );
        }
        return $query;
    }

} 