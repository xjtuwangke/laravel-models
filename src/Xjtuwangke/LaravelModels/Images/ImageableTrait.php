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

    public $modifiedImage = null;

    public static function _onBoot_imageableTrait(){
        static::saved( function( $item ){
            if( null !== $item->modifiedImage ){
                $url = $item->modifiedImage;
                ImageModel::linkUploadedImage( $item , $url , 0 );
                $item->modifiedImage = null;
            }
            return $item;
        });
    }

    public function image(){
        return $this->morphOne( 'Xjtuwangke\LaravelModels\Images\ImageModel' , 'imageable' )->where( 'image_order' , '0' );
    }

    public static function bindFormActionImageUpload( $form , $item = null , $id = 0){

        $label = '封面图';

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

        $form->setSaveFunc( 'image' , function( $item , $form , $field ){
            $url = $field->value();
            $item->modifiedImage = $url;
            return $item;
        });
        if( $id ){
            $image = $item->getImage();
            $form->setDefault( 'image' , $item->getImage() );
        }
        return $form;
    }

    public function getImage(){
        $image = $this->image;
        if( $image ){
            $url = $image->url;
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