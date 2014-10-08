<?php

namespace Xjtuwangke\LaravelModels\Images;

use Xjtuwangke\LaravelModels\BasicModel;

class ImageModel extends BasicModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'images';

    public function scopeOfGroup( $query , $group ){
        return $query->where( 'images.imageable_type' , $group );
        //User::ofGroup('admin')->get()
    }

    public function imageable(){
        return $this->morphTo();
    }

    /**
     *
     * 图片上传的流程:
     * 1. UploadController负责move,resize,生成URL,调用 ImageModel::createUploadedImage,将图片存入数据库
     * 2. 表单提交后 调用 ImageModel::linkUploadedImage 建立关联
     * @param $file 图片相对于 public/upload/ 的相对路径 + 文件名
     * @param $url  图片的url地址,可能是CDN
     * @return static
     */
    public static function createUploadedImage( $file , $url ){
        $fullpath = public_path( $file );
        $size = filesize( $fullpath );
        list( $width , $height ) = getimagesize( $fullpath );
        $explode = explode( '.' , $file );
        $ext = end( $explode );
        $image = static::create( [
            'image' => $file , 'ext' => $ext , 'file_size' => $size , 'width' => $width , 'height' => $height , 'url' => $url
        ]);
        return $image;
    }

    /**
     * @param $linked
     * @param $url
     * @param $order
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function linkUploadedImage( $linked , $url , $order ){
        $images = static::where( 'imageable_id' , $linked->id )
            ->where( 'imageable_type' , get_class( $linked ) )
            ->where( 'image_order' , $order )->get();
        foreach( $images as $image ){
            $image->delete();
        }
        $image = static::withTrashed()->where( [ 'url' => $url ] )->first();
        if( $image && $linked ){
            $image->imageable_id = $linked->id;
            $image->imageable_type = get_class( $linked );
            $image->image_order = $order;
            $image->save();
            $image->restore();
        }
        return $image;
    }

    /**
     * @param $linked
     */
    public static function unlinkAllImages( $linked ){
        $images = static::where( 'imageable_id' , $linked->id )
            ->where( 'imageable_type' , get_class( $linked ) )
            ->where( 'image_order' , '!=' , 0 )->get();
        foreach( $images as $image ){
            $image->delete();
        }
    }

}
