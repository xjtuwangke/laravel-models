<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-9-8
 * Time: 9:20
 */

namespace Xjtuwangke\LaravelModels;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;

class AuthModel extends \Auth {

    static public function attempt( $credentials = array(), $remember = false, $login = true ){
        $result = parent::attempt( $credentials , $remember , $login );
        if( $result ){
            Session::set( 'auth_field' , Config::get( 'auth.model') );
        }
        return $result;
    }

    static public function getUser(){
        if( is_null( Session::get( 'auth_field' ) ) || Session::get( 'auth_field' ) !==  Config::get( 'auth.model') ){
            return null;
        }
        else{
            return parent::getUser();
        }
    }

    static public function user(){
        if( Session::get( 'auth_field' ) !==  Config::get( 'auth.model') ){
            return null;
        }
        else{
            return parent::user();
        }
    }

    static public function login( $user, $remember = false ){
        Session::set( 'auth_field' , Config::get( 'auth.model') );
        return parent::login( $user, $remember );
    }
}