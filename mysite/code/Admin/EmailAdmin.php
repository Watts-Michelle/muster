<?php

class EmailAdmin extends ModelAdmin {

    public static $managed_models = array(
        'MusterEmail'
    );

    static $url_segment = 'email';

    static $menu_title = 'Contact Emails';
}