<?php

class CreateFavoritesAndLiksTable extends \Xjtuwangke\LaravelModels\Migration\BasicMigration {

	protected $tables = [ 'Xjtuwangke\LaravelModels\Favorites\FavoriteModel' , 'Xjtuwangke\LaravelModels\Favorites\VisitModel' , 'Xjtuwangke\LaravelModels\Favorites\LikeModel' ];

}
