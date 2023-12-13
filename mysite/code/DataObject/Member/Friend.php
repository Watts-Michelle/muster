<?php

class Friend extends DataObject
{
    public static $db = array(
        'Status' => 'Enum("Pending, Accepted, Rejected, Blocked, Deleted")',
        'InviterBlocked' => 'Boolean',
        'InviteeBlocked' => 'Boolean'
    );

    private static $has_one = array(
        'Inviter' => 'Member',
        'Invitee' => 'Member'
    );
    
    /**
     * Friend response
     *
     * @return array
     */
    public function getData()
    {
        if($friend = Member::get()->byID($this->InviteeID)){

            $user = [
                'uid' => $friend->UUID,
                'friend_member_id' => $friend->ID,
                'inviter_block' => $this->InviterBlocked,
                'invitee_block' => $this->InviteeBlocked,
                'status' => $this->Status,
                'username' => $friend->Username,
                'facebook_name' => $friend->FacebookName,
                'profile_image' => $friend->getUserImage(),
                'location' => $friend->Location,
                'location_latitude' => $friend->LocationLatitude,
                'location_longitude' => $friend->LocationLongitude,
//                'joined_date' => (int)$this->dbObject('JoinedDate')->format('U'),
                'user_status' => $friend->UserStatus,
                'localgamingstore_account' => $friend->LocalGamingStoreAccount()->Status ? $friend->LocalGamingStoreAccount()->Status : false,
            ];

            return $user;
        }
    }
    
    public function onAfterWrite()
    {
        parent::onAfterWrite();
        
        apc_delete('member-'.$this -> InviteeID);
        apc_delete('member-'.$this -> InviterID);
    }
    
    public function onAfterDelete() {
        
        apc_delete('member-'.$this -> InviteeID);
        apc_delete('member-'.$this -> InviterID);
        
        parent::onAfterDelete();
    }

}