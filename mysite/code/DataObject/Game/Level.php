<?php

class Level extends DataObject
{
    public static $db = array();

    private static $has_one = array(
        'Game' => 'Game'
    );

    private static $has_many = array(
        'ExperiencePoints' => 'ExperiencePoint'
    );

    private static $summary_fields = array();

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        return $fields;
    }

}