<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-8-14
 * Time: 19:25
 */

namespace Xjtuwangke\LaravelModels\Traits;

trait OneHasManyTrait {

    protected function setManyModels( $column , $objects = '*' ){
        if( '*' === $objects ){
            $this->$column = '*';
        }
        else{
            $string = '';
            foreach( $objects as $one ){
                $string.= $one->id . ';';
            }
            $this->$column = $string;
        }
        $this->save();
        return $this;
    }

    protected function getManyModels( $model , $column ){
        if( '*' === $this->$column ){
            return $model;
        }
        else{
            $list = explode( ';' , $this->$column );
            return $model->whereIn( 'id' , $list );
        }
    }

    protected function isManyIncludesOne( $object , $column ){
        if( '*' === $this->$column ){
            return true;
        }
        else{
            $list = explode( ';' , $this->$column );
            foreach( $list as $id ){
                if( $id === $object->id ){
                    return true;
                }
            }
            return false;
        }
    }

    protected function isManyOnlyHasOne( $object , $column ){
        if( '*' === $this->$column ){
            return false;
        }
        else{
            $list = explode( ';' , $this->$column );
            if( count( $list ) == 1 && in_array( $object->id , $list ) ){
                return true;
            }
            else{
                return false;
            }
        }
    }

    protected function isManyHasOtherThan( $object , $column ){
        if( '*' === $this->$column ){
            return true;
        }
        else{
            $list = explode( ';' , $this->$column );
            foreach( $list as $id ){
                if( $id !== $object->id ){
                    return true;
                }
            }
            return false;
        }
    }





}