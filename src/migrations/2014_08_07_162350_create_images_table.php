<?php

/**
 *创建 tables:
 * images    图片信息表
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// images表，储存图片信息 用于imageable接口
        Schema::create( 'images' , function( Blueprint $table ){
            $table->engine = 'InnoDB';
            $table->increments( 'id' );
            $table->char( 'image' , 100 );
            $table->char( 'ext' , 100 );
            $table->char( 'file_size' , 100 );
            $table->char( 'width' , 100 );
            $table->char( 'height' , 100 );
            $table->text( 'url' );
            $table->morphs( 'imageable' );
            $table->string( 'type' )->default( 'default' );
            $table->integer( 'image_order' )->default( 0 );
            $table->softDeletes();
            $table->timestamps();
        });

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
        Schema::dropIfExists( 'images' );
	}

}
