<?php

class UsersAdmin extends ModelAdmin {

    public static $managed_models = array(
        'Member'
    );

    static $url_segment = 'users';

    static $menu_title = 'Users';
    
//    public function getExportFields() {
//        return [
//            'FirstName' => 'FirstName',
//            'LocationLongitude' => 'LocationLongitude',
//            'LocationLatitude' => 'LocationLatitude',
//        ];
//    }
}