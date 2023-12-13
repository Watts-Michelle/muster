<?php

class ExperiencePoint extends DataObject
{
    public static $db = array(
        'Points' => 'Int',
    );

    public static $has_one = array(
        'GamingSessionResult' => 'GamingSessionResult',
        'Commend' => 'Commend',
    );

    private static $summary_fields = array();

    public function getTitle() {
        return $this->Points;
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        return $fields;
    }

}