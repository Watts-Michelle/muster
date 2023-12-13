<?php

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class Location extends DataObject
{
    public static $db = array(
        'UUID' => 'Varchar(50)',
        'Location' => 'Varchar(255)',
        'LocationLatitude' => 'Float',
        'LocationLongitude' => 'Float'
    );

    private static $has_many = array(
        'GamingSessions' => 'GamingSession'
    );

    protected static $indexes = array(
        'UUID' => 'unique("UUID")'
    );

    private static $summary_fields = array(
        'VenueImage.StripThumbnail' => 'VenueImage',
        'Name' => 'Name',
        'Location' => 'Location',
        'LocationLatitude' => 'Latitude',
        'LocationLongitude' => 'Longitude'
    );

    public function onAfterWrite()
    {
        parent::onAfterWrite();

        if (!$this->UUID) {
            $uuid = Uuid::uuid4();
            $this->UUID = $uuid->toString();
            $this->write();
        }
    }

    public function getTitle() {
        return $this->Location;
    }
    

    public function getData()
    {
        $location = [
            'uid' => $this->UUID,
            'location' => $this->Location,
            'location_latitude' => $this->LocationLatitude,
            'location_longitude' => $this->LocationLongitude,
        ];

        return $location;
    }
    
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->fieldByName('Root.Main.UUID')->setattribute('readonly', true);
        return $fields;
    }


}