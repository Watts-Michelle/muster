<?php

class SiteOptions extends ModelAdmin {

    public static $managed_models = array(
        'Badge', 'GamingSession', 'Venue', 'Game'
    );

    static $url_segment = 'siteoptions';

    static $menu_title = 'SiteOptions';
}