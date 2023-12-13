<?php

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class MemberExtension extends DataExtension
{
    public static $db = array(
        'UUID' => 'Varchar(50)',
        'Username' => 'Varchar',
        'FacebookUserID' => 'Varchar(255)',
        'FacebookName' => 'Varchar',
        'Location' => 'Varchar(255)',
        'LocationLatitude' => 'Float',
        'LocationLongitude' => 'Float',
        'LocalGamingStoreAccount' => 'Boolean',
        'JoinedDate' => 'SS_DateTime',
        'LoginCount' => 'Int',
        'UserStatus' => "Enum('Active, Deleted, Ready, Defeated, Ensorceled, Offline')",
        'PushToken' => 'Varchar(255)',
        'Hidden' => 'Boolean',
        'ConsecutiveDays' => 'Int',
    );

    private static $has_one = array(
        'ProfileImage' => 'Image',
        'LocalGamingStoreAccount' => 'LocalGamingStoreAccount'
    );

    private static $has_many = array(
        'Inviters' => 'Friend.Inviter',
        'Invitees' => 'Friend.Invitee',
        'Blockers' => 'BlockedUser.Blocker',
        'Blockees' => 'BlockedUser.Blockee',
        'Reporters' => 'ReportUser.Reporter',
        'Reportees' => 'ReportUser.Reportee',
        'Notifications' => 'Notification',
        'GamingSessions' => 'GamingSession',
        'GamingSessionResults' => 'GamingSessionResult',
        'LoginCounts' => 'LoginCount',
        'InvitationSenders' => 'GamingSessionInvitation.InvitationSender',
        'InvitationRecipients' => 'GamingSessionInvitation.InvitationRecipient',
        'RequestSenders' => 'GamingSessionJoinRequest.RequestSender',
        'RequestRecipients' => 'GamingSessionJoinRequest.RequestRecipient',
        'Commends' => 'Commend',
    );

    private static $many_many = array(
        'Games' => 'Game',
        'Badges' => 'Badge'
    );

    private static $many_many_extraFields = array(
        "Games" => array('Owned' => 'Boolean', 'Played' => 'Boolean'),
    );

    protected static $indexes = array(
        'UUID' => 'unique("UUID")',
        'Username' => 'unique("Username")',
        'FacebookUserID' => 'unique("FacebookUserID")',
    );

    private static $searchable_fields = array();

    private static $summary_fields = array(
        'Username' => 'Username'
    );

    public function onAfterWrite()
    {
        parent::onAfterWrite();

        if (!$this->owner->UUID) {
            $uuid = Uuid::uuid4();
            $this->owner->UUID = $uuid->toString();
            $this->owner->JoinedDate = $this->owner->Created;
            $this->owner->write();
        }  
        
        apc_delete('member-'.$this -> owner -> ID);
    }
    
    public function onAfterDelete() {
        
        apc_delete('member-'.$this -> owner -> ID);
        
        parent::onAfterDelete();
    }

    public function updateCMSFields(FieldList $fields)
    {
        $fields->fieldByName('Root.Main.ProfileImage')->setFolderName('Users/'.$this->owner->UUID );
        $fields->fieldByName('Root.Main.UUID')->setattribute('readonly', true);
        $fields->fieldByName('Root.Main.PushToken')->setattribute('readonly', true);
        $fields->fieldByName('Root.Main.LoginCount')->setattribute('readonly', true);
        $fields->fieldByName('Root.Main.FacebookName')->setattribute('readonly', true);
        $fields->fieldByName('Root.Main.FacebookUserID')->setattribute('readonly', true);
        $fields->removeByName('LocalGamingStoreAccountID');
        $fields->removeByName('JoinedDate');
        $field = DatetimeField_Readonly::create('Created', 'Joined Date', $this->owner->Created);
        $fields->insertBefore('LoginCount', $field);
        $fields->insertBefore('LastVisited', $fields->fieldByName('Root.Main.LocalGamingStoreAccount'));
        
        if($this->owner->LocalGamingStoreAccount) {
            $fields->fieldByName('Root.Main.Location')->setattribute('readonly', true);
            $fields->fieldByName('Root.Main.LocationLatitude')->setattribute('readonly', true);
            $fields->fieldByName('Root.Main.LocationLongitude')->setattribute('readonly', true);
        }

        $config = GridFieldConfig_RelationEditor::create();

        $config->removeComponentsByType(new GridFieldDetailForm());
        $config->removeComponentsByType(new GridFieldDataColumns());
        $config->removeComponentsByType(new GridFieldAddNewButton());

        $edittest = new GridFieldDetailForm();
        $edittest->setFields(FieldList::create(
            TextField::create('ManyMany[Owned]', 'Owned')
        ));

        $summaryfieldsconf = new GridFieldDataColumns();
        $summaryfieldsconf->setDisplayFields(array(
            'Owned' => 'Owned'
        ));

        $config->addComponent($edittest);
        $config->addComponent($summaryfieldsconf, new GridFieldFilterHeader());

        $field = GridField::create('Games', null, $this->owner->Games(), $config);
        $fields->addFieldToTab('Root.Owned', $field);

        return $fields;
    }

    /**
     * Check if user (member) is friends with a player (friend).
     *
     * @param $member
     * @param $friend
     * @return bool
     */
    public function checkIsFriend($member, $friend)
    {
        $f = Friend::get()->filter(array('InviterID' => $member->ID, 'InviteeID' => $friend->ID))->sort('Created DESC')->first();
        
        if ($f && ($f->Status == 'Accepted')) {
            
            return true;

        }

        $f = Friend::get()->filter(array('InviterID' => $friend->ID, 'InviteeID' => $member->ID))->sort('Created DESC')->first();

        if ($f && ($f->Status == 'Accepted')) {

            return true;

        }

        return false;
    }
    
    public function checkMessageRequest($current_user, $user)
    {
        $request = MessageRequest::get() -> filter(array('RequesterID' => $current_user -> ID, 'RecieverID' => $user -> ID, 'Accepted' => 1 ));
        if(!!$request->count()){
            return 1;
        }
        
        $request = MessageRequest::get() -> filter(array('RequesterID' => $user -> ID, 'RecieverID' => $current_user -> ID, 'Accepted' => 1 ));
        if(!!$request->count()){
            return 1;
        }
        
        $request = MessageRequest::get() -> filter(array('RequesterID' => $current_user -> ID, 'RecieverID' => $user -> ID, 'Accepted' => [0, -1] ));
        
        if(!!$request->count()){
            return -1;
        }
        
        $request = MessageRequest::get() -> filter(array('RecieverID' => $current_user -> ID, 'RequesterID' => $user -> ID, 'Accepted' => [0, -1] ));
        
        if(!!$request->count()){
            return -2;
        } else {
            return 0;
        }
        
        
    }

    /**
     * Check if user (member) has been blocked by a player (blocker).
     *
     * @param $member
     * @param $friend
     * @return bool
     */
    public function checkUserIsBlockee($member, $blocker)
    {
        // Get all blocked records where the user is the blockee (has been blocked by a player).
        if($BlockedUsers = BlockedUser::get()->filter('BlockeeID', $member->ID)){

            foreach($BlockedUsers as $blockedUser){

                // If blocked records BlockerID matches the blocker (player).
                if($blockedUser->BlockerID == $blocker->ID){

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if user (member) has blocked a player (blockee).
     *
     * @param $member
     * @param $blocker
     * @return bool
     */
    public function checkUserIsBlocker($member, $blockee)
    {
        // Get all blocked records where the user is the blocker (has blocked a player).
        if($BlockedUsers = BlockedUser::get()->filter('BlockerID', $member->ID)){

            foreach($BlockedUsers as $blockedUser){

                // If blocked records BlockeeID matches the blockee (player).
                if($blockedUser->BlockeeID == $blockee->ID){

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get member image
     * - image is pulled from facebook unless user has uploaded one from their phone.
     *
     * @return null|string
     */
    public function getUserImage()
    {
        $image = null;

        if ($this->owner->ProfileImage()->ID) {
            $image = $this->owner->ProfileImage()->SetWidth(400)->AbsoluteURL;
        }

        if($image == null && $this->owner->LocalGamingStoreAccount) {
            $image = Director::absoluteBaseURL().'assets/LGS.png';
        } elseif ($image == null && $this->owner->FacebookUserID) {
            
            $image = $this->getFbImage();
        }

        return $image;
    }

    public function getFbImage() {
        
        $opts = [
            "http" => [
                "method" => "GET",
                "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36\r\n"
            ]
        ];

        $context = stream_context_create($opts);
        $image = 'https://graph.facebook.com/' . $this->owner->FacebookUserID . '/picture?width=400&height=400';
            
        $imageFile = file_get_contents($image, false, $context);

        $base = Director::baseFolder();
        $parentFolder = Folder::find_or_make('Users' .'/' . $this->owner->UUID . '/image');
        // Generate default filename
        $nameFilter = FileNameFilter::create();
        $fileName = $nameFilter->filter($this->owner->UUID.'.jpg');
        $fileName = basename($fileName);

        $relativeFolderPath = $parentFolder
                        ? $parentFolder->getRelativePath()
                        : ASSETS_DIR . '/';
        $relativeFilePath = $relativeFolderPath . $fileName;

        $fileClass = File::get_class_for_file_extension(pathinfo($this->owner->UUID.'.jpg', PATHINFO_EXTENSION));
        $file = new $fileClass();

        $fileSuffixArray = explode('.', $fileName);
        $fileTitle = array_shift($fileSuffixArray);
        $fileSuffix = !empty($fileSuffixArray)
                        ? '.' . implode('.', $fileSuffixArray)
                        : null;

        // make sure files retain valid extensions
        $oldFilePath = $relativeFilePath;
        $relativeFilePath = $relativeFolderPath . $fileTitle . $fileSuffix;
        if($oldFilePath !== $relativeFilePath) {
                user_error("Couldn't fix $relativeFilePath", E_USER_ERROR);
        }
        while(file_exists("$base/$relativeFilePath")) {
                $i = isset($i) ? ($i+1) : 2;
                $oldFilePath = $relativeFilePath;

                $prefix = '';
                $pattern = '/' . preg_quote($prefix) . '([0-9]+$)/';
                if(preg_match($pattern, $fileTitle, $matches)) {
                        $fileTitle = preg_replace($pattern, $prefix . ($matches[1] + 1), $fileTitle);
                } else {
                        $fileTitle .= $prefix . $i;
                }
                $relativeFilePath = $relativeFolderPath . $fileTitle . $fileSuffix;

                if($oldFilePath == $relativeFilePath && $i > 2) {
                        user_error("Couldn't fix $relativeFilePath with $i tries", E_USER_ERROR);
                }
        }

        if(file_put_contents("$base/$relativeFilePath", $imageFile)) {
            $file->ParentID = $parentFolder ? $parentFolder->ID : 0;
            $file->Name = basename($relativeFilePath);
            $file->write();
            $this->owner->ProfileImageID = $file->ID;
            $this->owner->write();
            $image = $this->owner->ProfileImage()->SetWidth(400)->AbsoluteURL;
        }
        
        return $image;
    }
    
    public function getUserThumbnailImage()
    {
        $image = null;

        if ($this->owner->ProfileImage()->ID) {
            $image = $this->owner->ProfileImage()->SetWidth(50)->AbsoluteURL;
        }

        if($image == null && $this->owner->LocalGamingStoreAccount) {
            $image = Director::absoluteBaseURL().'assets/LGS.png';
        } elseif ($image == null && $this->owner->FacebookUserID) {
            
            $opts = [
                "http" => [
                    "method" => "GET",
                    "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36\r\n"
                ]
            ];

            $context = stream_context_create($opts);
            
            $image = 'https://graph.facebook.com/' . $this->owner->FacebookUserID . '/picture?width=400&height=400';
            
            $imageFile = file_get_contents($image, false, $context);
            
            $base = Director::baseFolder();
            $parentFolder = Folder::find_or_make('Users' .'/' . $this->owner->UUID . '/image');
            // Generate default filename
            $nameFilter = FileNameFilter::create();
            $fileName = $nameFilter->filter($this->owner->UUID.'.jpg');
            $fileName = basename($fileName);
            
            $relativeFolderPath = $parentFolder
                            ? $parentFolder->getRelativePath()
                            : ASSETS_DIR . '/';
            $relativeFilePath = $relativeFolderPath . $fileName;

            $fileClass = File::get_class_for_file_extension(pathinfo($this->owner->UUID.'.jpg', PATHINFO_EXTENSION));
            $file = new $fileClass();

            $fileSuffixArray = explode('.', $fileName);
            $fileTitle = array_shift($fileSuffixArray);
            $fileSuffix = !empty($fileSuffixArray)
                            ? '.' . implode('.', $fileSuffixArray)
                            : null;

            // make sure files retain valid extensions
            $oldFilePath = $relativeFilePath;
            $relativeFilePath = $relativeFolderPath . $fileTitle . $fileSuffix;
            if($oldFilePath !== $relativeFilePath) {
                    user_error("Couldn't fix $relativeFilePath", E_USER_ERROR);
            }
            while(file_exists("$base/$relativeFilePath")) {
                    $i = isset($i) ? ($i+1) : 2;
                    $oldFilePath = $relativeFilePath;

                    $prefix = '';
                    $pattern = '/' . preg_quote($prefix) . '([0-9]+$)/';
                    if(preg_match($pattern, $fileTitle, $matches)) {
                            $fileTitle = preg_replace($pattern, $prefix . ($matches[1] + 1), $fileTitle);
                    } else {
                            $fileTitle .= $prefix . $i;
                    }
                    $relativeFilePath = $relativeFolderPath . $fileTitle . $fileSuffix;

                    if($oldFilePath == $relativeFilePath && $i > 2) {
                            user_error("Couldn't fix $relativeFilePath with $i tries", E_USER_ERROR);
                    }
            }

            if(file_put_contents("$base/$relativeFilePath", $imageFile)) {
                $file->ParentID = $parentFolder ? $parentFolder->ID : 0;
                $file->Name = basename($relativeFilePath);
                $file->write();
                $this->owner->ProfileImageID = $file->ID;
                $this->owner->write();
                $image = $this->owner->ProfileImage()->SetWidth(50)->AbsoluteURL;
            }
            
        }

        return $image;
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
    
       

    /**
     * Member response
     *
     * @param array|null $params
     * @return array
     */
    public function getData($full = true, array $params = null)
    {
        $this->checkUserIsBlocker(CurrentUser::getUser(), $this->owner) ? $blocked = 1 : $blocked = 0;
        $this->checkIsFriend(CurrentUser::getUser(), $this->owner) ? $friend = 1 : $friend = 0;
        $messageRequestState = $this -> checkMessageRequest(CurrentUser::getUser(), $this->owner);

        $xp = $this->calculateXP();
        
        if($full) {
            
            $sessionsPlayed = $this->getUserPlayedSessions();
            $user = [
                'chat_id' => $this -> owner -> ID,
                'uid' => $this->owner->UUID,
                'blocked_by_current_user' => $blocked,
                'friend_with_currend_user' => $friend,
                'can_message_current_user' => $friend ? 1 : $messageRequestState,
                'username' => $this->owner->Username,
                'facebook_name' => $this->owner->FacebookName,
                'profile_image' => $this->owner->getUserImage(),
                'profile_thumbnail' => $this->owner->getUserThumbnailImage(),
                'location' => $this->owner->Location,
                'location_latitude' => (float)$this->owner->LocationLatitude,
                'location_longitude' => (float)$this->owner->LocationLongitude,
                'joined_date' => (int)$this->owner->dbObject('Created')->format('U'),
                'sessions_played' => $sessionsPlayed,
                'xp' => $xp,
                'level' => self::expToLevel($xp),
                'user_status' => $this->owner->UserStatus, // Should only be visible to users who are friends!
                //'localgamingstore_account' => $this->owner->LocalGamingStoreAccount() ? $this->owner->LocalGamingStoreAccount()->Status : false,
                'localgamingstore_account' => $this -> owner -> LocalGamingStoreAccount,
                'hidden' => $friend ? 0 : $this -> owner-> Hidden,
            ];

            if($params != null) {

                $GamingSessionsData = ['gamingsessions' => []];

                $FriendsData = ['friends' => []];

                $PlayedGamesData = ['played_games' => []];

                $OwnedGamesData = ['owned_games' => []];

                $SessionInvitationsData = ['session_invitations' => []];

                $JoinRequestsData = ['join_requests' => []];

                $merge = array();

                if (in_array('gamingsessions', $params)) {

                    foreach ($this->owner->GamingSessions()->sort('Created ASC') as $gamingsession) {
                        if ($data = $gamingsession->getData()) {
                            $GamingSessionsData['gamingsessions'][] = $data;
                        }
                    }

                    $merge = array_merge($merge, $GamingSessionsData);
                }

                if (in_array('friends', $params)) {

                    $FriendsData['friends'] = $this->getFriends();
                    $merge = array_merge($merge, $FriendsData);
                    
                }

                if(in_array('playedgames', $params)){

                    foreach ($this->owner->Games()->filter('Played', true) as $game) {
                        if ($data = $game->getData($this->owner)) {
                            $PlayedGamesData['played_games'][] = $data;
                        }
                    }

                    $merge = array_merge($merge, $PlayedGamesData);
                }

                if(in_array('ownedgames', $params)){

                    foreach ($this->owner->Games()->filter('Owned', true) as $game) {
                        if ($data = $game->getData($this->owner)) {
                            $OwnedGamesData['owned_games'][] = $data;
                        }
                    }

                    $merge = array_merge($merge, $OwnedGamesData);
                }

                if(in_array('sessioninvitations', $params)){

                    foreach(GamingSessionInvitation::get()->filter('InvitationRecipientID', $this->owner->ID) as $invitation){
                        $SessionInvitationsData['session_invitations'][] = ['uid' => $invitation->UUID, 'invitation_status' => $invitation->Status];
                    }

                    $merge = array_merge($merge, $SessionInvitationsData);
                }

                if(in_array('joinrequests', $params)){

                    foreach(GamingSessionJoinRequest::get()->filter('RequestSenderID', $this->owner->ID) as $joinrequest){
                        $JoinRequestsData['join_requests'][] = ['uid' => $joinrequest->UUID, 'invitation_status' => $joinrequest->Status];
                    }

                    $merge = array_merge($merge, $JoinRequestsData);
                }

                $merge = array_merge($user, $merge);

                return $merge;
            }

            
        } else {
            
            $user = [
                'chat_id' => $this -> owner -> ID,
                'uid' => $this->owner->UUID,
                'blocked_by_current_user' => $blocked,
                'friend_with_currend_user' => $friend,
                'can_message_current_user' => $friend ? 1 : $messageRequestState ,
                'username' => $this->owner->Username,
                'facebook_name' => $this->owner->FacebookName,
                'profile_image' => $this->owner->getUserImage(),
                'profile_thumbnail' => $this->owner->getUserThumbnailImage(),
                'location' => $this->owner->Location,
                'location_latitude' => (float)$this->owner->LocationLatitude,
                'location_longitude' => (float)$this->owner->LocationLongitude,
                'joined_date' => (int)$this->owner->dbObject('Created')->format('U'),
                'xp' => $xp,
                'level' => self::expToLevel($xp),
                'user_status' => $this->owner->UserStatus, // Should only be visible to users who are friends!
//                'localgamingstore_account' => $this->owner->LocalGamingStoreAccount() ? $this->owner->LocalGamingStoreAccount()->Status : false,
                'localgamingstore_account' => $this -> owner -> LocalGamingStoreAccount,
                'hidden' => $friend ? 0 : $this -> owner-> Hidden,
            ];
        }
        
        return $user;
    }
    
    public static function expToLevel($exp)
    {
        $level = floor($exp / 100);
        
        if($level < 1)
        {
            return 1;
        }
        elseif($level > 10)
        {
            return 10;
        }
        
        return $level;
    }
    
    public function augmentSQL(SQLQuery &$query) {
        if (!is_a(Controller::curr(), 'Authorisation_Controller')) {
            $query->addWhere(['"Member"."UserStatus" != \'Deleted\'']);
        }
    }
    
    public function getUserPlayedSessions() {
        $PlayedSessionsData = [];
        
        foreach ($this->owner->GamingSessions()->filter(['GameStatus' => 'Completed'])->sort('Created ASC') as $gamingsession) {
            if ($data = $gamingsession->getData()) {
                
                $PlayedSessionsData[] = $data;
            }
        }
        
        foreach ($this->owner->GamingSessions()->filter(['GameStatus' => ['Active', 'Cancelled'], 'EndDate:LessThan' => date('U')])->sort('Created ASC') as $gamingsession) {
            if ($data = $gamingsession->getData()) {
                
                $PlayedSessionsData[] = $data;
            }
        }
        
        foreach(GamingSessionInvitation::get()->filter(['Status' => 'Accepted', 'InvitationRecipientID' => $this->owner->ID]) as $item){
           
            $gamingSession = $item->GamingSession();
            
            if ($gamingSession->GameStatus === 'Completed' && $data = $gamingSession->getData()) {
                
                $PlayedSessionsData[] = $data;
            }
        }
        
        foreach(GamingSessionInvitation::get()->filter(['Status' => 'Accepted', 'InvitationRecipientID' => $this->owner->ID]) as $item){
           
            $gamingSession = $item->GamingSession();
            
            if (in_array($gamingSession->GameStatus, ['Active', 'Cancelled']) && ((int)$gamingSession->dbObject('EndDate')->format('U') <  date('U')) && $data = $gamingSession->getData()) {
                
                $PlayedSessionsData[] = $data;
            }
        }

        foreach(GamingSessionJoinRequest::get()->filter(['Status' => 'Accepted', 'RequestSenderID' => $this->owner->ID]) as $item){
            
            $gamingSession = $item->GamingSession();
            
            if ($gamingSession->GameStatus === 'Completed' && $data = $gamingSession->getData()) {
                
                $PlayedSessionsData[] = $data;
            }
        }
        
        foreach(GamingSessionJoinRequest::get()->filter(['Status' => 'Accepted', 'RequestSenderID' => $this->owner->ID]) as $item){
            
            $gamingSession = $item->GamingSession();
            
            if (in_array($gamingSession->GameStatus, ['Active', 'Cancelled']) && ((int)$gamingSession->dbObject('EndDate')->format('U') <  date('U')) && $data = $gamingSession->getData()) {
                
                $PlayedSessionsData[] = $data;
            }
        }
        
        return $PlayedSessionsData;
    }
    
    public function getUserCurrentSessions() {
        $CurrentSessionsData = [];
        
        foreach ($this->owner->GamingSessions()->filter(['GameStatus' => ['Active', 'Cancelled'], 'StartDate:LessThan' => date('U'), 'EndDate:GreaterThan' => date('U')])->sort('Created ASC') as $gamingsession) {
            if ($data = $gamingsession->getData()) {
                
                $CurrentSessionsData[] = $data;
            }
        }
        
        foreach(GamingSessionInvitation::get()->filter(['Status' => 'Accepted', 'InvitationRecipientID' => $this->owner->ID]) as $item){
           
            $gamingSession = $item->GamingSession();
            
            if (((int)$gamingSession->dbObject('StartDate')->format('U') <  date('U')) && ((int)$gamingSession->dbObject('EndDate')->format('U') >  date('U')) && in_array($gamingSession->GameStatus, ['Active', 'Cancelled']) && $data = $gamingSession->getData()) {
                
                $CurrentSessionsData[] = $data;
            }
        }

        foreach(GamingSessionJoinRequest::get()->filter(['Status' => 'Accepted', 'RequestSenderID' => $this->owner->ID]) as $item){
            
            $gamingSession = $item->GamingSession();
            
            if (((int)$gamingSession->dbObject('StartDate')->format('U') <  date('U')) && ((int)$gamingSession->dbObject('EndDate')->format('U') >  date('U')) && in_array($gamingSession->GameStatus, ['Active', 'Cancelled']) && $data = $gamingSession->getData()) {
                
                $CurrentSessionsData[] = $data;
            }
        }
        
        return $CurrentSessionsData;
    }
    
    public function getUserFutureSessions() {
        $FutureSessionsData = [];
        
        foreach ($this->owner->GamingSessions()->filter(['GameStatus' => ['Active', 'Cancelled'], 'StartDate:GreaterThan' => date('U')])->sort('Created ASC') as $gamingsession) {
            if ($data = $gamingsession->getData()) {
                
                $FutureSessionsData[] = $data;
            }
        }
        
        foreach(GamingSessionInvitation::get()->filter(['Status' => 'Accepted', 'InvitationRecipientID' => $this->owner->ID]) as $item){
           
            $gamingSession = $item->GamingSession();
            
            if ( ((int)$gamingSession->dbObject('StartDate')->format('U') >  date('U')) && in_array($gamingSession->GameStatus, ['Active', 'Cancelled']) && $data = $gamingSession->getData()) {
                
                $FutureSessionsData[] = $data;
            }
        }

        foreach(GamingSessionJoinRequest::get()->filter(['Status' => 'Accepted', 'RequestSenderID' => $this->owner->ID]) as $item){
            
            $gamingSession = $item->GamingSession();
            
            if ( ((int)$gamingSession->dbObject('StartDate')->format('U') >  date('U')) && in_array($gamingSession->GameStatus, ['Active', 'Cancelled']) && $data = $gamingSession->getData()) {
                
                $FutureSessionsData[] = $data;
            }
        }
        
        return $FutureSessionsData;
    }
    
    public function getUserCancelledSessions() {
        $CancelledSessionsData = [];
        
        foreach ($this->owner->GamingSessions()->filter(['GameStatus' => 'Cancelled'])->sort('Created ASC') as $gamingsession) {
            if ($data = $gamingsession->getData()) {
                
                $CancelledSessionsData[] = $data;
            }
        }
        
        foreach(GamingSessionInvitation::get()->filter(['Status' => 'Accepted', 'InvitationRecipientID' => $this->owner->ID]) as $item){
           
            $gamingSession = $item->GamingSession();
            
            if ( ($gamingSession->GameStatus === 'Cancelled') && $data = $gamingSession->getData()) {
                
                $CancelledSessionsData[] = $data;
            }
        }

        foreach(GamingSessionJoinRequest::get()->filter(['Status' => 'Accepted', 'RequestSenderID' => $this->owner->ID]) as $item){
            
            $gamingSession = $item->GamingSession();
            
            if ( ($gamingSession->GameStatus === 'Cancelled') && $data = $gamingSession->getData()) {
                
                $CancelledSessionsData[] = $data;
            }
        }
        
        return $CancelledSessionsData;
    }
    
    public function calculateXP(array $sessions = []) {
        $exp = 0;
        
        if(empty($sessions)) {
            
             foreach($this->owner->GamingSessionResults() as $result) {
                 
                      $exp += $result->calculateXP();
             }
            
        } else {
            
            foreach($sessions as $session) {

                $sessionObj = GamingSession::get()->filter(['UUID' => $session['uid']])->first();
                $result = $sessionObj->GamingSessionResults()->filter(['MemberID' =>  $this->owner->ID])->first();

                if($result) {

                    $exp += $result->calculateXP();
                    
                }
            }
        }
        
        return $exp;
    }
    
    public function getFriends() {
        $FriendsData = [];
        $invitedUsers = [];
        $inviteeUsers = [];
        
        foreach(Friend::get()->filter(['InviterID' => $this->owner->ID, 'Status' => 'Accepted']) as $item){
            $invitedUsers[] = $item->InviteeID;
        }
        
        $invitedUsers = array_unique($invitedUsers);
        
        foreach($invitedUsers as $invitedUser) {
            $lastFriendRequest = Friend::get()->filter(['InviterID' => $this->owner->ID, 'InviteeID' => $invitedUser])->sort('Created DESC')->first();
            if($lastFriendRequest->Status == 'Accepted') {
                $invitee = Member::get()->byID($invitedUser);
                 if($invitee) {
                    // Check if user (member) has blocked a player (blockee).
                    $this->owner->checkUserIsBlocker( $this->owner, $invitee) ? $blocked = 1 : $blocked = 0;

                    $d = [
                        'uid' => $invitee->UUID,
                        'member_id' => $invitee->ID, // TO DO: remove, for testing purposes only!
                        'blocked_by_current_user' => $blocked,
                        'username' => $invitee->Username,
                        'facebook_name' => $invitee->FacebookName,
                        'profile_image' => $invitee->getUserImage(),
                        'location' => $invitee->Location,
                        'location_latitude' => $invitee->LocationLatitude,
                        'location_longitude' => $invitee->LocationLongitude,
                        'joined_date' => (int)$invitee->dbObject('Created')->format('U'),
                        'user_status' => $invitee->UserStatus,
                        //'localgamingstore_account' => $invitee->LocalGamingStoreAccount()->Status ? $invitee->LocalGamingStoreAccount()->Status : false,
                        'localgamingstore_account' => $invitee -> LocalGamingStoreAccount,
                    ];

                    $FriendsData[$d['member_id']] = $d;
                }
            }
            
        }
        
        foreach(Friend::get()->filter(['InviteeID' => $this->owner->ID, 'Status' => 'Accepted']) as $item){
            $inviteeUsers[] = $item->InviterID;
        }
        
        $inviteeUsers = array_unique($inviteeUsers);
        
        foreach($inviteeUsers as $inviteeUser) {
            $lastFriendRequest = Friend::get()->filter(['InviteeID' => $this->owner->ID, 'InviterID' => $inviteeUser])->sort('Created DESC')->first();
            if($lastFriendRequest->Status == 'Accepted') {
                $inviter = Member::get()->byID($inviteeUser);
                 if($inviter) {
                    // Check if user (member) has blocked a player (blockee).
                    $this->owner->checkUserIsBlocker( $this->owner, $inviter) ? $blocked = 1 : $blocked = 0;

                    $d = [
                        'uid' => $inviter->UUID,
                        'member_id' => $inviter->ID, // TO DO: remove, for testing purposes only!
                        'blocked_by_current_user' => $blocked,
                        'username' => $inviter->Username,
                        'facebook_name' => $inviter->FacebookName,
                        'profile_image' => $inviter->getUserImage(),
                        'location' => $inviter->Location,
                        'location_latitude' => $inviter->LocationLatitude,
                        'location_longitude' => $inviter->LocationLongitude,
                        'joined_date' => (int)$inviter->dbObject('Created')->format('U'),
                        'user_status' => $inviter->UserStatus,
                        //'localgamingstore_account' => $invitee->LocalGamingStoreAccount()->Status ? $invitee->LocalGamingStoreAccount()->Status : false,
                        'localgamingstore_account' => $inviter -> LocalGamingStoreAccount,
                    ];

                    $FriendsData[$d['member_id']] = $d;
                }
            }
            
        }
        
        //normalize keys
        $normalKeys = array_values($FriendsData);
        
        return $normalKeys;
    }
    
    public function markAsActive() {
        if((int)date('d') - (int)$this->owner->dbObject('LastVisited')->format('d') === 1) {

            $this->owner->LastVisited =  date('Y-m-d H:i:s', strtotime(SS_Datetime::now()->getValue()));
            $this->owner->ConsecutiveDays += 1;
            $this->owner->write();
            
            $this->addZealotBadge();

        } else if(!$this->owner->LastVisited || ((int)date('d') - (int)$this->owner->dbObject('LastVisited')->format('d') > 1)) {

            $this->owner->LastVisited =  date('Y-m-d H:i:s', strtotime(SS_Datetime::now()->getValue()));
            $this->owner->ConsecutiveDays = 1;
            $this->owner->write();

        }
    }
    
    public function addZealotBadge() {
        if($this->owner->ConsecutiveDays >= 5 && ($badge = Badge::get()->filter(['Name' => 'Zealot'])->first()) ) {
            $this->owner->Badges()->add($badge->ID);
            return true;
        } else {
            return false;
        }
    }
    
    public function addFounderBadge() {
        $founderEndDate = '2018-03-31 23:59:59';
        if(($founderEndDate > $this->owner->Created) && ($badge = Badge::get()->filter(['Name' => 'Founder'])->first())) {
            $this->owner->Badges()->add($badge->ID);
            return true;
        } else {
            return false;
        }
    }
    
    public function getDailyXP() {
        $xp = 0;
        foreach ($this->owner->GamingSessionResults()->filter(['Created:PartialMatch' => date('Y-m-d')]) as $result) {
           $xp += $result->ExperiencePoint()->Points;
        }
        foreach ($this->owner->Commends()->filter(['Created:PartialMatch' => date('Y-m-d')]) as $commend) {
           $xp += $commend->ExperiencePoint()->Points;
        }
        return $xp;
    }
    
    public function refreshFlags($data) {
        static $member = null;
        static $members = [];
        static $blocked = [];
        static $friends = [];
        static $message = [];
		
        array_walk_recursive($data, function (&$item, $key) use (&$member, &$members, &$blocked, &$friends, &$message)
        {
            if($key === 'chat_id') {
                $member = $item;
                if(!in_array($item, array_keys($members))) {
                        $members[$member] =  Member::get()->byID($item);
                }
            } elseif($key === 'blocked_by_current_user') {
                if(in_array($member, array_keys($blocked))) {
                    $item = $blocked[$member];
                } else {
                    $item = isset($members[$member]) && $members[$member]->checkUserIsBlocker(CurrentUser::getUser(), $members[$member]) ? 1 : 0;
                    $blocked[$member] = $item;
                }
            } elseif($key === 'friend_with_currend_user') {		
                if(in_array($member, array_keys($friends))) {
                    $item = $friends[$member];
                } else {
                    $item = isset($members[$member]) && $members[$member]->checkIsFriend(CurrentUser::getUser(), $members[$member]) ? 1 : 0;
                    $friends[$member] = $item;
                }		
            } elseif($key === 'can_message_current_user') {
                if(in_array($member, array_keys($message))) {
                    $item = $message[$member];
                } else {
                    $messageRequestState = isset($members[$member]) ? $members[$member] -> checkMessageRequest(CurrentUser::getUser(), $members[$member]) : 0 ;
                    $item = !empty($friends[$member]) ? 1 : $messageRequestState;
                    $message[$member] = $item;
                }
            }
        });
		
        return $data;
    }
    
}