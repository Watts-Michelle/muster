<?php

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class GamingSessionInvitation extends DataObject
{
    public static $db = array(
        'UUID' => 'Varchar(50)',
        'Status' => 'Enum("Pending, Accepted, Rejected, Cancelled")',
    );

    private static $has_one = array(
        'GamingSession' => 'GamingSession',
        'InvitationSender' => 'Member',
        'InvitationRecipient' => 'Member'
    );

    public function onAfterWrite()
    {
        parent::onAfterWrite();

        if (!$this->owner->UUID) {
            $uuid = Uuid::uuid4();
            $this->owner->UUID = $uuid->toString();
            $this->owner->write();
        }
        
        apc_delete('member-'.$this -> InvitationSenderID);
        apc_delete('member-'.$this -> InvitationRecipientID);
        
    }
    
    public function onAfterDelete() {
        
        apc_delete('member-'.$this -> InvitationSenderID);
        apc_delete('member-'.$this -> InvitationRecipientID);
        
        parent::onAfterDelete();
    }

    public function getData()
    {
        $invitation = [
            'uid' => $this->UUID,
            'invitation_status' => $this->Status,
            'invitation_sender' => Member::get()->byID($this->InvitationSenderID)->getData(),
            'gaming_session' => GamingSession::get()->byID($this->GamingSessionID)->getData()
        ];

        return $invitation;
    }
    
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->fieldByName('Root.Main.UUID')->setattribute('readonly', true);
        return $fields;
    }

}