<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14/10/23
 * Time: 05:27
 */

namespace Xjtuwangke\LaravelModels;


class DummyModel extends BasicModel{

    protected static $withModelObserver = false;

    protected $table = 'dummy';

}