<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-8-14
 * Time: 21:03
 */

namespace Xjtuwangke\LaravelModels\Migration;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use Illuminate\Support\Facades\Schema;

class BasicMigration extends Migration{

    protected $tables = array(
        //'UserModel' , 'ProfileModel'
    );

    public function up(){
        foreach( $this->tables as $model ){
            Schema::create( $model::getTableName() , function( Blueprint $table ) use( $model ){
                $model::_schema( $table );
            });
        }
    }

    public function down(){
        $tables = $this->tables;
        $tables = array_reverse( $tables );
        foreach( $tables as $model ){
            Schema::dropIfExists( $model::getTableName() );
        }
    }

}