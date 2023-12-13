<?php

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class MusterEmail extends DataObject
{
    public static $db = array(
        'UUID' => 'Varchar(50)',
        'Sent' => 'Boolean',
        'Message' => 'Text',
        'Type' => 'Enum("ContactUs")',
    );

    private static $has_one = array(
        'Sender' => 'Member',
        'Receiver' => 'Member'
    );

    private static $summary_fields = array(
        'Sender.name' => 'Sender',
        'Message' => 'Message',
        'Created' => 'Created'
    );
    
    
    public function onAfterWrite()
    {
        parent::onAfterWrite();

        if (!$this->owner->UUID) {
            $uuid = Uuid::uuid4();
            $this->owner->UUID = $uuid->toString();
            $this->owner->write();
        }
    }
    
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->fieldByName('Root.Main.UUID')->setattribute('readonly', true);
        return $fields;
    }

}