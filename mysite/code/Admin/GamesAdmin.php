<?php

class GamesAdmin extends ModelAdmin {

    public static $managed_models = array(
        'Game'
    );

    static $url_segment = 'games';

    static $menu_title = 'Games';
    
    public function getExportFields() {
        return [
            'objectname' => 'objectname',
            'minplayers' => 'minplayers',
            'maxplayers' => 'maxplayers',
            'playingtime' => 'playingtime',
            'objectid' => 'objectid',
            'rating' => 'rating',
            'weight' => 'weight',
            'own' => 'own',
            'fortrade' => 'fortrade',
            'want' => 'want',
            'wanttobuy' => 'wanttobuy',
            'wanttoplay' => 'wanttoplay',
            'preowned' => 'preowned',
            'preordered' => 'preordered',
            'wishlist' => 'wishlist',
            'wishlistpriority' => 'wishlistpriority',
            'collid' => 'collid',
            'baverage' => 'baverage',
            'average' => 'average',
            'avgweight' => 'avgweight',
            'rank' => 'rank',
            'numowned' => 'numowned',
            'objecttype' => 'objecttype',
            'originalname' => 'originalname',
            'maxplaytime' => 'maxplaytime',
            'minplaytime' => 'minplaytime',
            'yearpublished' => 'yearpublished',
            'bgglanguagedependence' => 'bgglanguagedependence',
        ];
    }
}