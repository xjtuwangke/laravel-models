<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use Xjtuwangke\LaravelModels\Tags\TagModel;

class CreateTagsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
        Schema::create( 'tags' , function( Blueprint $table ){
            $table->engine = 'InnoDB';
            $table->increments( 'id' );
            TagModel::_schema( $table );
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
        Schema::dropIfExists( 'tags' );
	}

}
