<?php

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class Venue extends DataObject
{
    public static $db = array(
        'UUID' => 'Varchar(50)',
        'Name' => 'Varchar',
        'Location' => 'Varchar(255)',
        'LocationLatitude' => 'Float',
        'LocationLongitude' => 'Float'
    );

    private static $has_one = array(
        'VenueImage' => 'Image'
    );

    private static $has_many = array(
        'VenueOpeningHours' => 'VenueOpeningHour',
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
        return $this->Name;
    }
    
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->addFieldToTab('Root.Main', UploadField::create('VenueImage')->setFolderName('VenueImage'));
        $fields->fieldByName('Root.Main.UUID')->setattribute('readonly', true);

        return $fields;
    }

    public function getVenueImage()
    {
        $image = null;

        if ($this->VenueImage()->ID) {
            $image = $this->VenueImage()->SetWidth(400)->AbsoluteURL;
        }

        return $image;
    }

    public function getVenueThumbnailImage()
    {
        $image = null;

        if ($this->VenueImage()->ID) {
            $image = $this->VenueImage()->SetWidth(50)->AbsoluteURL;
        }

        return $image;
    }

    public function getData()
    {
        $game = [
            'uid' => $this->UUID,
            'name' => $this->Name,
            'image' => $this->getVenueImage(),
            'thumbnail' => $this->getVenueThumbnailImage(),
            'location' => $this->Location,
            'location_latitude' => $this->LocationLatitude,
            'location_longitude' => $this->LocationLongitude,
        ];

        return $game;
    }

}