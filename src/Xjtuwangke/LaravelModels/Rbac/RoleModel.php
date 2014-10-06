<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-8-24
 * Time: 1:41
 */

namespace Xjtuwangke\LaravelModels\Rbac;

use Xjtuwangke\LaravelModels\BasicModel;
use Xjtuwangke\LaravelModels\Traits\SwitchableTrait;

class RoleModel extends BasicModel{

    use SwitchableTrait;

    protected $table = 'roles';

    static public function _schema_roles( \Illuminate\Database\Schema\Blueprint $table ){
        $table->integer( 'parent_id' );
        $table->string( 'name' )->unique();
        $table->longText( 'permissions' );
        $table->string( 'title' );
        $table->text( 'desc' )->nullable();
        return $table;
    }

    public function parentRole(){
        return $this->hasOne( 'RoleModel' , 'parent_id' , 'id' );
    }

    public function isRoot(){
        if( $this->id == 1 ){
            return true;
        }
        else{
            return false;
        }
    }

    public static function getRoot(){
        return static::find(1);
    }

    public static function registerAllRoles(){
        $roles = static::all();
        foreach( $roles as $role ){
            $role->registerRole();
        }
        return $roles;
    }

    public function registerRole(){
        $name = $this->name;
        if( $this->parentRole ){
            $parent = [ $this->parentRole->name ];
        }
        else{
            $parent = [];
        }
        $permissions = explode( ';' , $this->permissions );
        RbacConfig::addRole( $name , $parent );
        foreach( $permissions as $action ){
            RbacConfig::addPermission( $name , $action );
        }
        return $this;
    }

    public function checkPermission( $action ){
        $actions = static::actionWithParents( $action );
        foreach( $actions as $action ){
            if( RbacConfig::isGranted( $this->name , $action ) ){
                return true;
            }
        }
        return false;
    }

    public static function actionToString( $action , $parameters = null ){
        if( null == $parameters ){
            return $action;
        }
        else{
            return $action . '#' . json_encode( $parameters );
        }
    }

    public static function actionWithParents( $action ){
        $results = [ $action ];
        $explode = explode( '#' , $action );
        $results[] = $explode[0];
        $explode = explode( '.' , $explode[0] );
        $path = [];
        foreach( $explode as $one ){
            $path[] = $one;
            $results[] = implode( '.' , $path );
            $results[] = implode( '.' , $path ) . '.*' ;
        }
        return $results;
    }

    public function setPermissions( $actions = [] ){
        $string = implode( ';' , $actions );
        $this->permissions = $string;
        $this->save();
        return $this;
    }

    public static function create( array $attributes ){
        $attributes['parent_id'] = 1;
        return parent::create( $attributes );
    }


} 