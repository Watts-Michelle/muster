<?php

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class GamingSessionJoinRequest extends DataObject
{
    public static $db = array(
        'UUID' => 'Varchar(50)',
        'Status' => 'Enum("Pending, Accepted, Rejected, Cancelled")',
    );

    private static $has_one = array(
        'GamingSession' => 'GamingSession',
        'RequestSender' => 'Member',
        'RequestRecipient' => 'Member'
    );

    public function onAfterWrite()
    {
        parent::onAfterWrite();

        if (!$this->owner->UUID) {
            $uuid = Uuid::uuid4();
            $this->owner->UUID = $uuid->toString();
            $this->owner->write();
        }
    
        apc_delete('member-'.$this -> RequestRecipientID);
        apc_delete('member-'.$this -> RequestSenderID);
        
    }
    
    public function onAfterDelete() {
        
        apc_delete('member-'.$this -> RequestRecipientID);
        apc_delete('member-'.$this -> RequestSenderID);
        
        parent::onAfterDelete();
    }

    public function getData()
    {
        $joinrequest = [
            'uid' => $this->UUID,
            'request_status' => $this->Status,
            'request_recipient' => Member::get()->byID($this->RequestSenderID)->getData(),
            'gaming_session' => GamingSession::get()->byID($this->GamingSessionID)->getData()
        ];

        return $joinrequest;
    }
    
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->fieldByName('Root.Main.UUID')->setattribute('readonly', true);
        return $fields;
    }

    
}