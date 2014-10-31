<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-8-17
 * Time: 4:04
 */

namespace Xjtuwangke\LaravelModels\Images;

use Illuminate\Support\Facades\Config;
use Xjtuwangke\LaravelCms\Elements\Form\FormField\FormFieldBase;

trait ImageableManyTrait {

    public $modifiedImages = array();

    public static function _onBoot_imageableManyTrait(){
        static::saved( function( $item ){
            foreach( $item->modifiedImages as $type => $images ){
                ImageModel::unlinkAllImages( $item , $type );
                $i = 1;
                foreach( $images as $image ){
                    ImageModel::linkUploadedImage( $item , $image , $i , $type );
                    $i++;
                }
            }
            $item->modifiedImages = array();
            return $item;
        });
    }

    public function images( $type = null ){
        if( is_null( $type ) ){
            return $this->morphMany( 'Xjtuwangke\LaravelModels\Images\ImageModel' , 'imageable' )->where( 'image_order' , '!=' , '0' )->orderBy( 'image_order' , 'asc' )->where( 'type' , 'default' );
        }
        else{
            return $this->morphMany( 'Xjtuwangke\LaravelModels\Images\ImageModel' , 'imageable' )->where( 'image_order' , '!=' , '0' )->orderBy( 'image_order' , 'asc' )->where( 'type' , $type )->get();
        }
        // related name type('imagable_type') id('imagable_id') localKey ('id')
        // select * from `images` where `images`.`deleted_at` is null and `images`.`imagable_id` = 1 and `images`.`imagable_type` = 'GoodsModel'
        // 返回一个relation而非collection
        // $goods->images() 返回collection
        // $goods->images 返回结果集
    }

    public static function bindFormActionImageManyUpload( $form , $item = null , $id = 0 , $type = 'default' ){

        $label = '其他图片';

        $width = null;
        $height = null;
        if( isset( static::$imageManyType ) ){
            $imageConfig = Config::get('images.' .  static::$imageManyType );
            if( $imageConfig ){
                $label = $imageConfig[2];
                $width = $imageConfig[0];
                $height = $imageConfig[1];
            }
        }

        $form->addField( FormFieldBase::createByType( 'images' , FormFieldBase::Type_MultiImage )
                ->setLabel( $label )
                ->setType('default')
                ->setDefault( [] )
                ->setWidth( $width )
                ->setHeight( $height )
        );

        $form->setSaveFunc( 'images' , function( $item , $form , $field ) use( $type ){
            $images = $field->value();
            $item->modifiedImages[ $type ] = $images;
            return $item;
        });
        if( $id ){
            $form->setDefault( 'images' , $item->getImagesArray( $type ) );
        }
        return $form;
    }

    public function getImagesArray( $type = 'default' ){
        $images = [];
        foreach( $this->images()->where( 'type' , $type )->get() as $image ){
            $images[] = $image->url;
        }
        return $images;
    }

    /**
     * 更新多图
     */
    public function updateMultiImages( array $urls ){
        ImageModel::unlinkAllImages( $this );
        $order = 1;
        $images = [];
        foreach( $urls as $url ){
            $images[] = ImageModel::linkUploadedImage( $this , $url , $order );
            $order++;
        }
        return $images;
    }

} 