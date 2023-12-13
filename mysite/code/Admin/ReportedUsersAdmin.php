<?php

class ReportedUsersAdmin extends ModelAdmin {

    public static $managed_models = array(
        'ReportUser'
    );

    static $url_segment = 'reportedusers';

    static $menu_title = 'Reported Users';
}