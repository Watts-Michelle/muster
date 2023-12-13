<?php

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class Badge extends DataObject
{
    public static $db = array(
        'UUID' => 'Varchar(50)',
        'Name' => 'Varchar',
        'Description' => 'HTMLText'
    );

    public static $has_one = array(
        'BadgeImage' => 'Image'
    );

    private static $belongs_many_many = array(
        'Members' => 'Member'
    );

    protected static $indexes = array(
        'UUID' => 'unique("UUID")',
    );

    private static $summary_fields = array(
        'BadgeImage.StripThumbnail' => 'Image',
        'Name' => 'Name',
        'Description' => 'Description',
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
    
    public function getBadgeImage()
    {
        $image = null;

        if ($this->BadgeImage()->ID) {
            $image = $this->BadgeImage()->SetWidth(400)->AbsoluteURL;
        }

        return $image;
    }
    
    public function getBadgeImageThumbnail()
    {
        $image = null;

        if ($this->BadgeImage()->ID) {
            $image = $this->BadgeImage()->SetWidth(50)->AbsoluteURL;
        }

        return $image;
    }

    public function getData()
    {
        $image = null;

        if ($this->BadgeImage()->ID) {
            $image = $this->BadgeImage()->AbsoluteURL;
        }
        
        $game = [
            'uid' => $this->UUID,
            'name' => $this->Name,
            'image' => $this->getBadgeImage(),
            'thumbnail' => $this->getBadgeImageThumbnail(),
            'description' => strip_tags($this->Description),
        ];

        return $game;
    }
    
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->fieldByName('Root.Main.UUID')->setattribute('readonly', true);
        return $fields;
    }


}
