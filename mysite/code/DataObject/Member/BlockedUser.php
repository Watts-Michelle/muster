<?php

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class BlockedUser extends DataObject
{
    public static $db = array(
        'UUID' => 'Varchar(50)',
        'Blocked' => 'Boolean'
    );

    private static $has_one = array(
        'Blocker' => 'Member',
        'Blockee' => 'Member'
    );

    protected static $indexes = array(
        'UUID' => 'unique("UUID")',
    );

    public function onAfterWrite()
    {
        parent::onAfterWrite();

        if (!$this->owner->UUID) {
            $uuid = Uuid::uuid4();
            $this->owner->UUID = $uuid->toString();
            $this->owner->write();
        }
        
        apc_delete('member-'.$this -> BlockeeID);
        apc_delete('member-'.$this -> BlockerID);
    }
    
    public function onAfterDelete() {
        
        apc_delete('member-'.$this -> BlockeeID);
        apc_delete('member-'.$this -> BlockerID);
        
        parent::onAfterDelete();
    }
    
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->fieldByName('Root.Main.UUID')->setattribute('readonly', true);
        return $fields;
    }

}