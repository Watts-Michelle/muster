<?php

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class GamingSession extends DataObject
{
    public static $db = array(
        'UUID' => 'Varchar(50)',
        'Name' => 'Varchar',
        'Description' => 'HTMLText',
        'StartDate' => 'SS_Datetime',
        'EndDate' => 'SS_DateTime',
        'PlayerLimit' => 'Int', // defaults to unlimited if not set
        'GameStatus' => 'Enum("Active, Completed, Cancelled")',
//        'Location' => 'Varchar(255)',
//        'LocationLatitude' => 'Float',
//        'LocationLongitude' => 'Float',
        'PrivacySetting' => 'Enum("Public, Private, Friends, "Public")',
        'Hidden' => 'Boolean', // only visible to host or invitees, unless hidden all users can view in app
        'Result' => 'Enum("\'\',Victory,Defeat,Stalemate,No Session")',
        'Recurring' => 'Enum(" \'\', Weekly, Fortnightly, Monthly")',
        'ChatUUID' => 'Varchar(50)'
    );

    private static $has_one = array(
        'GamingSessionImage' => 'Image',
        'Member' => 'Member',
        'Venue' => 'Venue',
        'Location' => 'Location',
        'Game' => 'Game',
    );

    private static $has_many = array(
        'GamingSessionInvitations' => 'GamingSessionInvitation',
        'GamingSessionJoinRequests' => 'GamingSessionJoinRequest',
        'GamingSessionResults' => 'GamingSessionResult'
    );

    protected static $indexes = array(
        'UUID' => 'unique("UUID")'
    );

    private static $summary_fields = array();

    public function getTitle() {
        return $this->Name;
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->fieldByName('Root.Main.GamingSessionImage')->setFolderName('GamingSessions/'.$this->owner->UUID );
//        $fields->addFieldToTab('Root.Main', DropdownField::create('PrivacySetting', 'Privacy Setting', $this->dbObject('PrivacySetting')->enumValues())->setEmptyString('-- Select a Setting --'));
        $fields->addFieldToTab('Root.Main', HtmlEditorField::create('Description')->setRows(3));
        $fields->fieldByName('Root.Main.UUID')->setattribute('readonly', true);

        $fields->replaceField('GameID', DropdownField::create('GameID','Game', Game::get()->map('ID', 'Title')));

        return $fields;
    }

    public function onAfterWrite()
    {
        parent::onAfterWrite();

        if (!$this->UUID) {
            $uuid = Uuid::uuid4();
            $this->UUID = $uuid->toString();
            $this->write();
        }
        
        apc_delete('member-'.$this -> MemberID);
        
    }
    
    public function onAfterDelete() {
        
        apc_delete('member-'.$this -> MemberID);
        
        parent::onAfterDelete();
    }

    /**
     * Get a list of gaming session attendees.
     *
     * @return ArrayList
     */
    public function getAttendees()
    {
        $list = new ArrayList();

        foreach($this->GamingSessionInvitations()->filter('Status', 'Accepted') as $item){
            $list->push(Member::get()->byID($item->InvitationRecipientID));
        }

        foreach($this->GamingSessionJoinRequests()->filter('Status', 'Accepted') as $request){
            $list->push(Member::get()->byID($request->RequestSenderID));
        }

        if(!$this->Member()->LocalGamingStoreAccount) {
            $list->push($this->Member());
        }
        
        return $list;
    }
    
    /**
     * Get a list of gaming session invited members.
     *
     * @return ArrayList
     */
    public function getInvited()
    {
        $list = new ArrayList();

        foreach($this->GamingSessionInvitations()->filter('Status', 'Pending') as $item){
            $list->push(Member::get()->byID($item->InvitationRecipientID));
        }

//        foreach($this->GamingSessionJoinRequests()->filter('Status', 'Pending') as $request){
//            $list->push(Member::get()->byID($request->RequestSenderID));
//        }
        
        return $list;
    }
    
    /**
     * Get a list of gaming session winners.
     *
     * @return ArrayList
     */
    public function getWinners()
    {
        $list = new ArrayList();

        foreach($this->GamingSessionResults()->filter('Result', 'Victory') as $item){
            $list->push(Member::get()->byID($item->MemberID));
        }

        return $list;
    }

    /**
     * Get current number of players in a session.
     *
     * @return int
     */
    public function getCurrentPlayerNumber()
    {
        $invitations = $this->GamingSessionInvitations()->filter('Status', 'Accepted');

        $joinRequests = $this->GamingSessionJoinRequests()->filter('Status', 'Accepted');

        return count($invitations) + count($joinRequests) + 1 /* author */;
    }

    /**
     * Check if a user ($Member) is an attendee of a gaming session ($Attendees).
     *
     * @param ArrayList $Attendees
     * @param $Member
     * @return bool
     */
    public function checkGamingSessionPlayer(ArrayList $Attendees, $Member)
    {
        foreach($Attendees as $attendee){

            if($attendee->ID == $Member->ID){

                return true;
            }
        }

        return false;
    }

    /**
     * Get gaming session image
     * - image is pulled from the game unless user has uploaded one from their phone.
     *
     * @return string
     */
    public function getGamingSessionImage()
    {
        $image = null;

        if ($this->GamingSessionImage()->ID) {
            $image = $this->GamingSessionImage()->SetWidth(400)->AbsoluteURL;
        }

        if ($image == null && $this->Game()->LogoID) {
            $image = $this->Game()->Logo()->SetWidth(400)->AbsoluteURL;
        }

        return $image;
    }
    public function getGamingSessionThumbnailImage()
    {
        $image = null;

        if ($this->GamingSessionImage()->ID) {
            $image = $this->GamingSessionImage()->SetWidth(50)->AbsoluteURL;
        }

        if ($image == null && $this->Game()->LogoID) {
            $image = $this->Game()->Logo()->SetWidth(50)->AbsoluteURL;
        }

        return $image;
    }

    /**
     * Gaming Session response
     *
     * @return array
     */
    public function getData()
    {
        $attending = [];
        $invited = [];
        $winners = [];
        
        foreach($this->getAttendees() as $attend) {
            if($attend) {
                $attending[] = $attend->getData(false);
            }
        }
        
        foreach($this->getInvited() as $inv) {
            if($inv) {
                $invited[] = $inv->getData(false);
            }
        }
        
        foreach($this->getWinners() as $winner) {
            if($winner) {
                $winners[] = $winner->getData(false);
            }
        }
        
//        $chat = DB::prepared_query('SELECT * FROM "cometchat_chatrooms" WHERE "cometchat_chatrooms"."guid" = ?', array($this->ChatUUID))->first();
        $chat = null;
        
        $data = [
            'uid' => $this->UUID,
            'name' => $this->Name,
            'description' => strip_tags($this->Description),
            'start_date' => (int)$this->dbObject('StartDate')->format('U'),
            'end_date' => (int)$this->dbObject('EndDate')->format('U'),
            'player_limit' => $this->PlayerLimit,
            'game_status' => $this->GameStatus,
            'privacy_setting' => $this->PrivacySetting,
            'hidden' => $this->Hidden,
            'recurring' => $this -> Recurring,
            'result' => $this->Result,
            'gamingsession_image' => $this->getGamingSessionImage(),
            'gamingsession_thumbnail' => $this->getGamingSessionThumbnailImage(),
            'host' => $this->Member()->exists() ? $this->Member()->getData(false) : false, //remove
            'venue' => $this->Venue()->exists() ?  $this->Venue()->getData() : false, //remove
            'location' => $this->Location()->exists() ? $this->Location()->getData() : false, //remove
            'game' => $this->Game()->exists() ? $this->Game()->getData() : false, //remove
            'attending' => $attending,
            'invited' => $invited,
            'request_status' => $this->calculateUserRequestStatus(),
            'winners' => $winners,
            'chat_uid' => $this->ChatUUID,
            'chat_id' => $chat ? $chat['id'] : '',
            'chat_name' => $chat ? $chat['name'] : '',
        ];

        return $data;
    }
    
    /**
     * Get distance between two lat/lng points.
     *
     * @param $OriginLat
     * @param $OriginLng
     * @param $DesLat
     * @param $DesLng
     * @return mixed
     */
    public function getDistance($OriginLat, $OriginLng, $DesLat, $DesLng)
    {
        $data = file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins={$OriginLat},{$OriginLng}&destinations={$DesLat},{$DesLng}&key=AIzaSyCMbs3cyu_1BO8d-QcpmBaFwlynFrfz1hw");

        $data = json_decode($data);
        
        if($data -> rows[0] -> elements[0] -> status == 'ZERO_RESULTS')
        {
            
            return null;
            
        } else if(!property_exists($data->rows[0]->elements[0], 'distance')) {
            
            return null;
            
        }

        return $data->rows[0]->elements[0]->distance->value;
    }
    
    public function calculateUserRequestStatus() {
        if(CurrentUser::getUser() && $this->Member()) {
            $member = CurrentUser::getUser();
            if($member->ID === $this->Member()->ID) {
                
                return 'OWNER';
                
            } elseif($member->InvitationRecipients()->filter(['GamingSessionID' => $this->ID, 'Status' => 'Accepted'])->exists()) {
                
                return 'GAME JOINED';
                
            } elseif($member->RequestSenders()->filter(['GamingSessionID' => $this->ID, 'Status' => 'Accepted'])->exists()) {
                
                return 'GAME JOINED';
                
            } elseif($member->InvitationRecipients()->filter(['GamingSessionID' => $this->ID, 'Status' => 'Pending'])->exists()) {
                
                return 'CONFIRM';
                
            } elseif($member->RequestSenders()->filter(['GamingSessionID' => $this->ID, 'Status' => 'Pending'])->exists()) {
                
                return 'REQUEST SENT';
                
            } else {
                
                return 'SEND REQUEST';
                
            }
            
        } else {
            
            return false;
            
        }
    }
}