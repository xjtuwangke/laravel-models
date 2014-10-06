<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-8-23
 * Time: 23:55
 */

namespace Xjtuwangke\LaravelModels\Rbac;

use Zend\Permissions\Rbac\Rbac;
use Zend\Permissions\Rbac\Role;

class RbacConfig {

    protected static $rbac = null;

    static public function instance(){
        if( ! static::$rbac ){
            static::$rbac = new Rbac();
        }
        return static::$rbac;
    }

    static public function addRole( $role , $parents = [] ){
        $rbac = static::instance();
        $role = new Role($role);
        if( ! is_array( $parents ) ){
            $parents = [ $parents ];
        }
        foreach( $parents as $parent ){
            if( ! $rbac->hasRole( $parent ) ){
                $rbac->addRole( new Role( $parent ) );
            }
        }
        $rbac->addRole( $role , $parents );
        return $rbac;
    }

    static public function getRole( $role ){
        return static::instance()->getRole( $role );
    }

    static public function addPermission( $role , $action ){
        return static::instance()->getRole( $role )->addPermission( $action );
    }

    static public function isGranted( $role , $action ){
        return static::instance()->isGranted( $role , $action );
    }


} 