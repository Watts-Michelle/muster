<?php

class LoginCount extends DataObject
{
    public static $db = array(
        'LoginDate' => 'SS_DateTime'
    );

    private static $has_one = array(
        'Member' => 'Member'
    );
}