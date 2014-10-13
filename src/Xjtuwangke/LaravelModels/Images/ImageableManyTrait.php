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

    public $modifiedImages = null;

    public static function _onBoot_imageableManyTrait(){
        static::saved( function( $item ){
            if( null !== $item->modifiedImages ){
                $images = $item->modifiedImages;
                ImageModel::unlinkAllImages( $item );
                $i = 1;
                foreach( $images as $image ){
                    ImageModel::linkUploadedImage( $item , $image , $i );
                    $i++;
                }
                $item->modifiedImages = null;
            }
            return $item;
        });
    }

    public function images(){
        return $this->morphMany( 'Xjtuwangke\LaravelModels\Images\ImageModel' , 'imageable' )->where( 'image_order' , '!=' , '0' )->orderBy( 'image_order' , 'asc' );
        // related name type('imagable_type') id('imagable_id') localKey ('id')
        // select * from `images` where `images`.`deleted_at` is null and `images`.`imagable_id` = 1 and `images`.`imagable_type` = 'GoodsModel'
        // 返回一个relation而非collection
        // $goods->images() 返回collection
        // $goods->images 返回结果集
    }

    public static function bindFormActionImageManyUpload( $form , $item = null , $id = 0){

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

        $form->setSaveFunc( 'images' , function( $item , $form , $field ){
            $images = $field->value();
            $item->modifiedImages = $images;
            return $item;
        });
        if( $id ){
            $form->setDefault( 'images' , $item->getImagesArray() );
        }
        return $form;
    }

    public function getImagesArray(){
        $images = [];
        foreach( $this->images as $image ){
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