<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-8-17
 * Time: 4:03
 */

namespace Xjtuwangke\LaravelModels\Images;

use Illuminate\Support\Facades\Config;
use Xjtuwangke\LaravelCms\Elements\Form\FormField\FormFieldBase;

trait ImageableTrait {

    public $modifiedImage = array();

    public static function _onBoot_imageableTrait(){
        static::saved( function( $item ){
            var_dump( $item->modifiedImage );
            foreach( $item->modifiedImage as $type => $url ){
                ImageModel::linkUploadedImage( $item , $url , 0 , $type );
            }
            $item->modifiedImage = array();
            return $item;
        });
    }

    public function image( $type = null ){
        if( is_null( $type ) ){
            return $this->morphOne( 'Xjtuwangke\LaravelModels\Images\ImageModel' , 'imageable' )->where( 'image_order' , '0' )->where( 'type' , 'default' );
        }
        else{
            return $this->morphOne( 'Xjtuwangke\LaravelModels\Images\ImageModel' , 'imageable' )->where( 'image_order' , '0' )->where( 'type' , $type )->first();
        }
    }

    public static function bindFormActionImageUpload( $form , $item = null , $id = 0 , $type = 'default'){

        if( 'default' === 'type' ){
            $label = '封面图';
        }
        else{
            $label = $type;
        }

        $width = null;
        $height = null;
        if( isset( static::$imageType )){
            $imageConfig = Config::get('images.' . static::$imageType );
            if( $imageConfig ){
                $label = $imageConfig[2];
                $width = $imageConfig[0];
                $height = $imageConfig[1];
            }
        }

        $form->addField( FormFieldBase::createByType( 'image' , FormFieldBase::Type_Image )
            ->setLabel( $label )
            ->setType('default')
            ->setDefault( '' )
            ->setWidth( $width )
            ->setHeight( $height )
        );

        $form->setSaveFunc( 'image' , function( $item , $form , $field )use( $type ){
            $url = $field->value();
            $item->modifiedImage[ $type ] =  $url;
            return $item;
        });
        if( $id ){
            $form->setDefault( 'image' , $item->getImage() );
        }
        return $form;
    }

    public function getImage(){
        $image = $this->image;
        if( $image ){
            return $image->url;
        }
        else{
            $image = static::find( 1 );
        }
        if( $image ){
            return $image->url;
        }
        else{
            return '';
        }
    }

    /**
     * 更新单图
     * @param $url
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function updateImage( $url ){
        return  ImageModel::linkUploadedImage( $this , $url , 0 );
    }

}