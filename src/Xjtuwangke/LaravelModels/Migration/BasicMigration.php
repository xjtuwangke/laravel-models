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

class BasicMigration extends Migration{

    protected $tables = array(
        //'users' => 'UserModel'
    );

    protected $model = null;

    public function up(){
        foreach( $this->tables as $tablename => $model ){
            $this->model = $model;
            Schema::create( $tablename , function( Blueprint $table ){
                $table->engine = 'InnoDB';
                $table->increments( 'id' );
                $method = 'schema_' . $table;
                $table = $this->$method( $table );
                $this->tables;
                $table = $this->schema( $table , $this->model );
                $table->softDeletes();
                $table->timestamps();
            });
        }
    }

    public function down(){
        $tables = $this->tables;
        $tables = array_reverse( $tables );
        foreach( $tables as $table => $model ){
            Schema::dropIfExists( $table );
        }

    }

    protected function schema_( $table ){

    }

    protected function schema( $table , $model ){
        $model = new $model();

        return $table;
    }

}