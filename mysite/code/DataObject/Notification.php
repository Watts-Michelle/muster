<?php

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class Notification extends DataObject
{
    public static $db = array(
        'UUID' => 'Varchar(50)',
        'Title' => 'Varchar(255)',
        'Message' => 'Varchar(255)',
        'Dismissed' => 'Boolean',
        'StoredData' => 'Text',
        'Type' => 'Varchar(255)',
    );

    private static $has_one = array(
        'Member' => 'Member'
    );
    
    protected static $indexes = array(
        'UUID' => 'unique("UUID")'
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

    public function getData()
    {
        $notification = [
            'uid' => $this->UUID,
            'title' => $this->Title,
            'message' => $this->Message,
            'date' => (int)$this->dbObject('Created')->format('U'),
            'data' => json_decode($this->StoredData, true),
            'type' => $this->Type
        ];

        return $notification;
    }
    
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->fieldByName('Root.Main.UUID')->setattribute('readonly', true);
        return $fields;
    }    
}