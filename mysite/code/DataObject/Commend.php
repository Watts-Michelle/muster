<?php

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class Commend extends DataObject
{
    public static $db = array(
        'UUID' => 'Varchar(50)',
    );

    private static $has_one = array(
        'GamingSessionResult' => 'GamingSessionResult',
        'ExperiencePoint' => 'ExperiencePoint',
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
    
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->fieldByName('Root.Main.UUID')->setattribute('readonly', true);
        return $fields;
    }

}