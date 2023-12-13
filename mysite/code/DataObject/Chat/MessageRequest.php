<?php

class MessageRequest extends DataObject
{
    public static $db = array(
        'Accepted' => 'Int'
    );

    private static $has_one = array(
        'Requester' => 'Member',
        'Reciever' => 'Member'
    );
    
    public function onAfterWrite()
    {
        parent::onAfterWrite();
        
        apc_delete('member-'.$this -> RequesterID);
        apc_delete('member-'.$this -> RecieverID);
    }
    
    public function onAfterDelete()
    {
        apc_delete('member-'.$this -> RequesterID);
        apc_delete('member-'.$this -> RecieverID);
        
        parent::onAfterDelete();
    }
    
}