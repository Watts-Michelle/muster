<?php

class Message extends DataObject {

    public static $db = array(
        'Title' => 'Varchar(255)',
        'Description' => 'HTMLText',
    );

    private static $has_one = array(
        'Chat' => 'Chat'
    );

    private static $summary_fields = array();

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        return $fields;
    }

}