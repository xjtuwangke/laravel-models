<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
        //创建histories表
        Schema::create( 'histories' , function( Blueprint $table ){
            $table->engine = 'InnoDB';
            $table->increments( 'id' );
            $table->string( 'operator_type' , 100 )->nullable();
            $table->string( 'operator_id' , 100 )->nullable();
            $table->morphs( 'resource' );
            $table->longText( 'table' );
            $table->text( 'action' );
            $table->longText( 'old' );
            $table->longText( 'new' );
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
        Schema::dropIfExists( 'histories' );
	}

}
