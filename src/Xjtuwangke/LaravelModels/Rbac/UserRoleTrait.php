<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-8-24
 * Time: 1:44
 */

namespace Xjtuwangke\LaravelModels\Rbac;

trait UserRoleTrait {

    static public function _schema_userwithroles( \Illuminate\Database\Schema\Blueprint $table ){
        $table->integer( 'role_id' );
        return $table;
    }

    public function role(){
        return $this->hasOne( 'Xjtuwangke\LaravelModels\Rbac\RoleModel' , 'id' , 'role_id' );
    }

    static public function createWithRole( $data , RoleModel $role ){
        $data[ 'role_id' ] = $role->id;
        return static::create( $data );
    }

} 