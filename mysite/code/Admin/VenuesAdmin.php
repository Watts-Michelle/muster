<?php

class VenuesAdmin extends ModelAdmin {

    public static $managed_models = array(
        'Venue'
    );

    static $url_segment = 'venues';

    static $menu_title = 'Venues';
}