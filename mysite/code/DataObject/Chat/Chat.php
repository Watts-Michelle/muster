<?php

class Chat extends DataObject {

    public static $db = array(
        'Title' => 'Varchar(255)',
        'Description' => 'HTMLText',
    );

    private static $has_one = array(
        'Member' => 'Member'
    );

    private static $has_many = array(
        'Messages' => 'Message'
    );

    private static $summary_fields = array();

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        return $fields;
    }

}