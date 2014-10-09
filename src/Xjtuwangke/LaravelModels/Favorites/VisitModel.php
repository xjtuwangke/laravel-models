<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14/10/9
 * Time: 04:56
 */

namespace Xjtuwangke\LaravelModels\Favorites;


use Illuminate\Database\Schema\Blueprint;
use Xjtuwangke\LaravelModels\Relationships\MtoNRelationShip;

class VisitModel extends MtoNRelationShip{

    protected $table = 'visits';

    protected static $nameM = 'user';

    protected static $nameN = 'item';

    public static function _schema( Blueprint $table ){
        $table = parent::_schema( $table );
        return $table;
    }
}