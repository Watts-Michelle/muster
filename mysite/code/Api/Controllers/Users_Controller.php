<?php

use Respect\Validation\Validator as v;

class Users_Controller extends Api_Controller {

    protected $auth = true;

    private static $allowed_actions = array(
        'handler',
        'getUserById',
        'userFriendRequest',
        'userFriendBatchRequest',
        'userAcceptFriendRequest',
        'userRejectFriendRequest',
        'userDeleteFriend',
        'userOwnedGames',
        'userPlayedGames',
        'userBadges',
        'getUserFriends',
        'localGamingStore',
        'userRequestLocalGamingStore',
        'userStatus',
        'userProfileImage',
        'getUserLocation',
        'getUserGamingSessions',
        'getUserGamingSessionInvitations',
        'getUserGamingSessionJoinRequests',
        'cancelGamingSessionAttendance',
        'userBlock',
        'getBlockedUsers',
        'userReport',
        'uidHandler',
        'getUserPotentialFriends',
        'pushToken',
        'getUserNotifications',
        'getUserNotification',
        'dismissUserNotification',
        'usersUnblock',
        'contactUs',
        'getUserGamingSessionsByTime',
        'requestLGS',
        'hide',
        'unhide',
        'setFacebookPhoto',
    );

    private static $url_handlers = array(
        '' => 'handler',                                                            // GET
        'pushtoken' => 'pushToken',                                                 // POST
        'contactus' => 'contactUs',                                                 // POST
        'requestlgs' => 'requestLGS',                                               // POST
        'hide' => 'hide',                                                           // POST
        'unhide' => 'unhide',                                                       // POST
        'facebookphoto' => 'setFacebookPhoto',                                      // POST
        'localgamingstore' => 'localGamingStore',                                   // GET
        '$UID/localgamingstore' => 'userRequestLocalGamingStore',                   // POST
        '$UID/ownedgames/$gameUID' => 'userOwnedGames',                             // PUT, DELETE, GET
        '$UID/playedgames/$gameUID' => 'userPlayedGames',                           // PUT, DELETE
        '$UID/badges/$badgeUID' => 'userBadges',                                    // GET, PUT, DELETE
        '$UID/friends/$friendUID/request' => 'userFriendRequest',                   // PUT
        '$UID/batchfriends' => 'userFriendBatchRequest',                            // PUT
        '$UID/friends/$friendUID/accept' => 'userAcceptFriendRequest',              // PUT
        '$UID/friends/$friendUID/reject' => 'userRejectFriendRequest',              // PUT
        '$UID/friends/$friendUID/delete' => 'userDeleteFriend',                     // DELETE
        '$UID/potentialfriends/$accessToken' => 'getUserPotentialFriends',          // GET
        '$UID/block/$blockeduserUID' => 'userBlock',                                // POST
        '$UID/unblock' => 'usersUnblock',                                           // POST
        '$UID/blockedusers' => 'getBlockedUsers',                                   // GET
        '$UID/report/$reporteduserUID' => 'userReport',                             // POST
        '$UID/friends' => 'getUserFriends',                                         // GET
        '$UID/status' => 'userStatus',                                              // GET, DELETE
        '$UID/profileimage' => 'userProfileImage',                                  // GET, POST
        '$UID/location' => 'getUserLocation',                                       // GET
        '$UID/gamingsessions/$mode' => 'getUserGamingSessions',                     // GET
        '$UID/gamingsessionsbytime' => 'getUserGamingSessionsByTime',               // GET
        '$UID/gamingsessioninvitations' => 'getUserGamingSessionInvitations',       // GET
        '$UID/gamingsessionjoinrequests' => 'getUserGamingSessionJoinRequests',     // GET
        '$UID/gamingsessionleave/$sessionUID' => 'cancelGamingSessionAttendance', //POST
        '$UID/notifications/$notificationUID/dismiss' => 'dismissUserNotification', // PUT
        '$UID/notifications/$notificationUID/one' => 'getUserNotification',         // GET
        '$UID/notifications' => 'getUserNotifications',                             // GET
        '$ID/byid' => 'getUserById',
        '$UID' => 'uidHandler',                                                     // GET, PUT, DELETE
    );


    /**
     * User Endpoints Handler
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function handler(SS_HTTPRequest $request)
    {
        if (! $request->isGet()) return $this->handleError(404, 'Must be a GET request');

        if($request->isGET()) {
            return $this->getAllUsers($request);
        }
    }

    /**
     * Get All Users
     * - search players by name and sort by game or location.
     *
     * - params example:
     * ?fieldset=sessions,friends,sessioninvitations,joinrequests&search_name=snow&filter=game,location&game_name=chess,clue&location_origin=51.336036,-0.2673819999999978&distance=40000
     *
     * @param SS_HTTPRequest $request
     * @return SS_HTTPResponse
     */
    public function getAllUsers(SS_HTTPRequest $request)
    {
        $fieldset = $request->getVar('fieldset') ? explode(',', $request->getVar('fieldset')) : null;
        $username = $request->getVar('search_name');
        $games = $request->getVar('game_uuid');
        
        $members = Member::get()->exclude('ID', CurrentUser::getUserID())->sort('Created ASC');
        
        $UsersData = ['users' => []];
        
        if($username)
        {
            $members = $members -> filterAny(array(
                'FirstName:PartialMatch' => $username,
                'Surname:PartialMatch' => $username,
                'Username:PartialMatch' => $username
            ));
        }
        
        $members = $members->innerJoin("Member_Games", "Member_Games.MemberID = Member.ID")
                -> where('Member_Games.Played != 0 or Member_Games.Owned != 0');
        if($games)
        {  
            $members = $members->innerJoin("Game", "Game.ID = Member_Games.GameID")
                    ->where('Game.UUID in (\''.implode('\',\'', explode(',',$games)).'\')')
                    -> where('Member_Games.Played = 1 ')
                    ;
        }

        if(($locationOrigin = $request->getVar('location_origin') ? explode(',', $request->getVar('location_origin')) : null) && $distance = $request->getVar('distance'))
        {
            $checkLocation = true;
            
        } else {
            
            $checkLocation = false;
            
        }
        
        foreach ($members as $member) {
            
//            if (!$member->checkUserIsBlockee(CurrentUser::getUser(), $member) && !$member->checkUserIsBlocker(CurrentUser::getUser(), $member)) {
                
                if ($checkLocation && !empty($member->LocationLatitude || $member->LocationLongitude)) {

                    $OriginDistance = $member->getDistance(
                            $locationOrigin[0], 
                            $locationOrigin[1], 
                            $member->LocationLatitude, 
                            $member->LocationLongitude
                    );

                    if (isset($OriginDistance) && ($OriginDistance <= $distance)) {
                        
                        $data = apc_fetch('member-'.$member->ID, $success);
                        if($success === true)
                        {
                            $data = $member->refreshFlags($data);
                            $UsersData['users'][] = $data;
                        }
                        elseif (
                        $data = $member->getData(true, $fieldset)
                                )
                        {
                            $UsersData['users'][] = $data;
                            apc_store('member-'.$member->ID, $data);
                        }
                    }
                    
                } else {
         
                    $data = apc_fetch('member-'.$member->ID, $success);
                    if($success === true)
                    {
                        $data = $member->refreshFlags($data);
                        $UsersData['users'][] = $data;
                    }
                    elseif (
                    $data = $member->getData(true, $fieldset)
                            )
                    {
                        $UsersData['users'][] = $data;
                        apc_store('member-'.$member->ID, $data);
                    }
                }
//            }
        }
        
        return (new JsonApi)->formatReturn($UsersData);
    }

    /**
     * User Endpoints uidHandler
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse|void
     * @throws O_HTTPResponse_Exception
     */
    public function uidHandler(SS_HTTPRequest $request)
    {
        if (!($request->isGET() || $request->isPUT() || $request->isDELETE())) return $this->handleError(404, 'Must be a GET or PUT or DELETE request');

        if($request->isGET()) {
            return $this->specificUser($request);
        }

        if($request->isPUT()) {
            return $this->updateUser($request);
        }

        if($request->isDELETE()) {
            return $this->deleteUser($request);
        }
    }

    /**
     * Get Specific User
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function specificUser(SS_HTTPRequest $request)
    {
        // PERMISSION - TO DO: Set restrictions on seeing session invitations and join requests??

        if (! $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $member = $this->checkUuidExists($request->param('UID'), 'Member')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        $arr = $request->getVar('fieldset') ? explode(',', $request->getVar('fieldset')) : null;

        return (new JsonApi)->formatReturn(['user' => $member->getData(true, $arr)]);
    }

        /**
     * Get Specific User
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function getUserById(SS_HTTPRequest $request)
    {
        // PERMISSION - TO DO: Set restrictions on seeing session invitations and join requests??

        if (! $id = $request->param('ID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $member = Member::get() ->byID($id)) {
            return $this->handleError(404, 'TO DO: Wrong ID!!');
        }

        $arr = $request->getVar('fieldset') ? explode(',', $request->getVar('fieldset')) : null;

        return (new JsonApi)->formatReturn(['user' => $member->getData(true, $arr)]);
    }
    
    /**
     * Update User
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function updateUser(SS_HTTPRequest $request)
    {
        if (! $UID = $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $member = $this->checkUuidExists($UID, 'Member')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        //  PERMISSION: Can only update user if they are logged in and UID matches current user
        if(! $this->isCurrentUser($UID)){
            return $this->handleError(404, 'TO DO: Whoops, you can not edit this user!');
        }

        $data = $this->getBody($request);
        
        $locationChanged = false;

        $userStatus = DB::get_schema()->enumValuesForField(new Member, 'UserStatus');

        if (isset($data['username']) && v::stringType()->validate($data['username'])) {
            $member->Username = $data['username'];
        }

        if (!$member->LocalGamingStoreAccount && isset($data['location']) && v::stringType()->validate($data['location'])) {
            $member->Location = $data['location'];
        }

        if (!$member->LocalGamingStoreAccount && isset($data['location_latitude']) && v::numeric()->validate($data['location_latitude'])) {
            $member->LocationLatitude = $data['location_latitude'];
            $locationChanged = true;
        }

        if (!$member->LocalGamingStoreAccount && isset($data['location_longitude']) && v::numeric()->validate($data['location_longitude'])) {
            $member->LocationLongitude = $data['location_longitude'];
            $locationChanged = true;
        }
        
        if (isset($data['first_name']) && v::stringType()->validate($data['first_name'])) {
            $member->FirstName = $data['first_name'];
        }
        
        if (isset($data['last_name']) && v::stringType()->validate($data['last_name'])) {
            $member->Surname = $data['last_name'];
        }

        if (isset($data['user_status']) && v::stringType()->in($userStatus)->validate($data['user_status'])) {
            $member->UserStatus = $data['user_status'];
        }
        
        if (isset($data['localgamingstore_account'])) {
            $member->LocalGamingStoreAccount = $data['localgamingstore_account'];
        }

        try {
            $member->write();
            if($locationChanged) {
                foreach($member->getFriends() as $friend) { 
                    if((abs($member->LocationLongitude - $friend['location_longitude']) < 0.1) && (abs($member->LocationLatitude - $friend['location_latitude']) < 0.1)) {
                        $Member = Member::get()->byID($friend['member_id']);
                        if(!empty($Member->PushToken)) {
                            $push = new Fcm;
                            $push->send($Member->PushToken, 'locationchange', [ 
                                'friend_uid' =>  $member->UUID,
                            ], 'Your friend is nearby!', $member->FirstName . ' ' . $member->Surname. ' has changed location to ' . $member->Location);
                        }
                    }
                }
            }
        } catch (Exception $e) {
            return $this->handleError(5000, $e->getMessage(), 400);
        }

        return (new JsonApi)->formatReturn(['user' => $member->getData()]);
    }

    /**
     * User Profile Image Endpoint Handler
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function userProfileImage(SS_HTTPRequest $request)
    {
        if (!($request->isGET() || $request->isPOST())) return $this->handleError(404, 'Must be a GET or POST request');

        if($request->isGET()) {
            return $this->getUserProfileImage($request);
        }

        if($request->isPOST()) {
            return $this->updateUserProfileImage($request);
        }
    }

    /**
     * Get User Profile Image
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function getUserProfileImage(SS_HTTPRequest $request)
    {
        // PERMISSION:  Can only view a users profile image if logged in and requested user has not blocked current user

        if (! $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $member = $this->checkUuidExists($request->param('UID'), 'Member')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        if($member->checkUserIsBlockee(CurrentUser::getUser(), $member)) {
            return $this->handleError(404, 'TO DO: Whoops, you are a blocked friend!!');
        }

        return (new JsonApi)->formatReturn(['profile_image' => $member->getUserImage()]);
    }

    /**
     * Update User Profile Image
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function updateUserProfileImage(SS_HTTPRequest $request)
    {
        // PERMISSION:  Can only update a users profile image if logged in and current user
        // look at validating image type - image library!

        if (! $UID = $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $member = $this->checkUuidExists($UID, 'Member')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        if(! $this->isCurrentUser($UID)){
            return $this->handleError(404, 'TO DO: Whoops, you can not edit this user!');
        }

        if($this->checkMultipartContentType($request->getHeader('Content-Type'))) {
            
            $image = $this->performUpload($_FILES['image'], $member, 'Users');

            if (! empty($image)) {
                $member->ProfileImageID = $image->ID;
            }

        } else {
            return $this->handleError(2003);
        }

        try {
            $member->write();
        } catch (Exception $e) {
            return $this->handleError(5000, $e->getMessage(), 400);
        }

        return (new JsonApi)->formatReturn([]);
    }

    /**
     * Get Local Gaming Store Users
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function localGamingStore(SS_HTTPRequest $request)
    {
        // PERMISSION:  Can only get LGSs if logged in.

        if (! $request->isGet()) return $this->handleError(404, 'Must be a GET request');

        $lgsData = [
            'users' => []
        ];

        foreach(Member::get()->sort('Created ASC') as $member) {

            if($member->LocalGamingStoreAccount){

                $data = apc_fetch('member-'.$member->ID, $success);
                if($success === true)
                {
                    $data = $member->refreshFlags($data);
                    $lgsData['users'][] = $data;
                }
                elseif (
                $data = $member->getData(true)
                        )
                {
                    $lgsData['users'][] = $data;
                    apc_store('member-'.$member->ID, $data);
                }
            }
        }
        return (new JsonApi)->formatReturn($lgsData);
    }

    /**
     * Request Local Gaming Store Account
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function userRequestLocalGamingStore(SS_HTTPRequest $request)
    {
        if (!$request->isPOST()) return $this->handleError(404, 'Must be a POST request');

        if (!($UID = $request->param('UID'))) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if ($UID != CurrentUser::getUserUUID()) {
            return $this->handleError(404, 'TO DO: Your user does not match the currently logged in user!');
        }

        if(! $member = $this->checkUuidExists($request->param('UID'), 'Member')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        $LGS = LocalGamingStoreAccount::create();
        $LGS->MemberID = $member->ID;
        $LGS->Status = 'Pending';
        $LGS->write();

        $member->LocalGamingStoreAccountID = $LGS->ID;
        $member->write();

        return (new JsonApi)->formatReturn([]);
    }

    /**
     * User Status Endpoint Handler
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function userStatus(SS_HTTPRequest $request)
    {
        if (!($request->isGET() || $request->isDELETE())) return $this->handleError(404, 'Must be a GET or DELETE request');

        if($request->isGET()) {
            return $this->getUserStatus($request);
        }

        if($request->isDELETE()) {
            return $this->deleteUserStatus($request);
        }
    }

    /**
     * Get User Status
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function getUserStatus(SS_HTTPRequest $request)
    {
        // PERMISSION:  User status can only be viewed by users logged in and are friends of user.

        if (! $UID = $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $member = $this->checkUuidExists($request->param('UID'), 'Member')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        if(! $member->checkIsFriend(CurrentUser::getUser(), $member)){
            return $this->handleError(404, 'TO DO: You have been blocked or are not friends!');
        }

        return (new JsonApi)->formatReturn(['user_status' => $member->UserStatus]);
    }

    /**
     * Delete User Status
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function deleteUserStatus(SS_HTTPRequest $request)
    {
        // PERMISSION:  User status can only be viewed by users logged in and are friends of user.

        if (! $UID = $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $member = $this->checkUuidExists($request->param('UID'), 'Member')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        //  PERMISSION: Can only update user if they are logged in and UID matches current user
        if(! $this->isCurrentUser($UID)){
            return $this->handleError(404, 'TO DO: Whoops, you can not edit this user!');
        }

        $member->UserStatus = '';
        $member->write();

        return (new JsonApi)->formatReturn([]);
    }

    /**
     * Get User Gaming Sessions
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function getUserGamingSessions(SS_HTTPRequest $request)
    {
        // PERMISSION:  Can only view a users profile image if logged in and requested user has not blocked current user

        if (! $request->isGET()) return $this->handleError(404, 'Must be a GET request');

        if (! $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $member = $this->checkUuidExists($request->param('UID'), 'Member')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        if($member->checkUserIsBlockee(CurrentUser::getUser(), $member)) {
            return $this->handleError(404, 'TO DO: Whoops, you are a blocked friend!!');
        }
        
        $mode = $request->param('mode');

        $GamingSessionData = [
            'gamingsessions' => []
        ];
        
        if($mode === 'past') {
            $timeCondition['StartDate:LessThan'] = date('U');
            
        } elseif($mode === 'future') {
            $timeCondition['StartDate:GreaterThan'] = date('U');
            
        } else {
            $timeCondition = [];
        }
        
        
        foreach(GamingSession::get()->filter(array_merge(['MemberID' => $member->ID], $timeCondition)) as $session) {
            if($data = $session->getData()) {
                $GamingSessionData['gamingsessions'][] = $data;
            }
        }
        foreach(GamingSessionInvitation::get()->filter(['Status' => 'Accepted', 'InvitationRecipientID' => $member->ID]) as $item){
           
            $gamingSession = $item->GamingSession();
            
            if(($mode === 'past') && ((int)$gamingSession->dbObject('StartDate')->format('U') < date('U')) && ($data = $gamingSession->getData())) {
                $GamingSessionData['gamingsessions'][] = $data;

            } elseif(($mode === 'future') && ((int)$gamingSession->dbObject('StartDate')->format('U') > date('U')) && ($data = $gamingSession->getData())) {
                $GamingSessionData['gamingsessions'][] = $data;

            } elseif(!$mode && ($data = $gamingSession->getData())) {
                $GamingSessionData['gamingsessions'][] = $data;
                
            }

        }

        foreach(GamingSessionJoinRequest::get()->filter(['Status' => 'Accepted', 'RequestRecipientID' => $member->ID]) as $item){
            
            $gamingSession = $item->GamingSession();
            
            if(($mode === 'past') && ((int)$gamingSession->dbObject('StartDate')->format('U') < date('U')) && ($data = $gamingSession->getData())) {
                $GamingSessionData['gamingsessions'][] = $data;

            } elseif(($mode === 'future') && ((int)$gamingSession->dbObject('StartDate')->format('U') > date('U')) && ($data = $gamingSession->getData())) {
                $GamingSessionData['gamingsessions'][] = $data;

            } elseif(!$mode && ($data = $gamingSession->getData())) {
                $GamingSessionData['gamingsessions'][] = $data;
                
            }
        }

        return (new JsonApi)->formatReturn($GamingSessionData);
    }
    
    /**
     * Get User Gaming Sessions divided by time
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function getUserGamingSessionsByTime(SS_HTTPRequest $request)
    {
        // PERMISSION:  Can only view a users profile image if logged in and requested user has not blocked current user

        if (! $request->isGET()) return $this->handleError(404, 'Must be a GET request');

        if (! $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $member = $this->checkUuidExists($request->param('UID'), 'Member')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        if($member->checkUserIsBlockee(CurrentUser::getUser(), $member)) {
            return $this->handleError(404, 'TO DO: Whoops, you are a blocked friend!!');
        }
        

        $GamingSessionData = [
            'gamingsessions' => [
                'past' =>  $member->getUserPlayedSessions(),
                'current' => $member->getUserCurrentSessions(),
                'future' =>  $member->getUserFutureSessions(),
                'cancelled' => $member->getUserCancelledSessions(),
            ]
        ];

        return (new JsonApi)->formatReturn($GamingSessionData);
    }
    
    /**
     * Get User Gaming Session Invitations
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function getUserGamingSessionInvitations(SS_HTTPRequest $request)
    {
        if (! $request->isGET()) return $this->handleError(404, 'Must be a GET request');

        if (! $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $member = $this->checkUuidExists($request->param('UID'), 'Member')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        $GamingSessionInvitationsData = [
            'session_invitations' => []
        ];

        foreach(GamingSessionInvitation::get()->filter('InvitationRecipientID', $member->ID) as $invitation) {
            $GamingSessionInvitationsData['session_invitations'][] = ['uid' => $invitation->UUID, 'invitation_status' => $invitation->Status];
        }

        return (new JsonApi)->formatReturn($GamingSessionInvitationsData);
    }

    /**
     * Get User Gaming Session Join Requests
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function getUserGamingSessionJoinRequests(SS_HTTPRequest $request)
    {
        if (! $request->isGET()) return $this->handleError(404, 'Must be a GET request');

        if (! $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $member = $this->checkUuidExists($request->param('UID'), 'Member')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        $GamingSessionJoinRequestData = [
            'join_requests' => []
        ];

        foreach(GamingSessionJoinRequest::get()->filter('RequestSenderID', $member->ID) as $joinrequest) {
            $GamingSessionJoinRequestData['join_requests'][] = ['uid' => $joinrequest->UUID, 'join_requests' => $joinrequest->Status];
        }

        return (new JsonApi)->formatReturn($GamingSessionJoinRequestData);
    }
    
    public function cancelGamingSessionAttendance(SS_HTTPRequest $request) 
    {
        if ( !( ($UID = $request->param('UID')) && ($sessionUID = $request -> param('sessionUID') ))) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }
                     
        if(! $member = $this->checkUuidExists($UID, 'Member')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }
        
        if(! $session = $this->checkUuidExists($sessionUID, 'GamingSession')) {
            return $this->handleError(404, 'TO DO: Wrong session UID!!');
        }

        $AttendanceUID = $member -> ID;
        $done = false;
        
        if($gameinvite = GamingSessionInvitation::get() -> filter(['GamingSessionID' => $session -> ID, 'InvitationRecipientID' => $member -> ID]) -> first())
        {
            $gameinvite -> Status = 'Cancelled';
            $gameinvite -> write();
            
            //REMOVE USER FROM CHAT
//            CometchatHelper::removeFromChatroom($session->ChatUUID, $gameinvite->InvitationRecipientID);
            
            $done = true;
        }  
        
        if($gamerequest = GamingSessionJoinRequest::get() -> filter(['GamingSessionID' => $session -> ID, 'RequestSenderID' => $member -> ID]) -> first())
        {
            $gamerequest -> Status = 'Cancelled';
            $gamerequest -> write();
            
            //REMOVE USER FROM CHAT
//            CometchatHelper::removeFromChatroom($session->ChatUUID, $gamerequest->RequestSenderID);
            
            $done = true;
        }
        
        if($done)
        {      
//            $chat = DB::prepared_query('SELECT * FROM "cometchat_chatrooms" WHERE "cometchat_chatrooms"."guid" = ?', [$session->ChatUUID])->first();
            $chat = null;
            if(!empty($session->Member()->PushToken)) {

                $push = new Fcm;
                $push->send($session->Member()->PushToken, 'gamingsession', [ 
                    'gamingsession_uid' => $session->UUID , 
                    'gamingsession_start_date' => (int)$session->dbObject('StartDate')->format('U'), 
                    'gamingsession_end_date' => (int)$session->dbObject('EndDate')->format('U'), 
                    'gamingsession_place_name' => $session->Venue()->exists() ? $session->Venue()->Location : $session->Location()->Location,
                    'gamingsession_name' => $session->Name, 
                    'gamingsession_host' => $session->Member() ? $session->Member()->FirstName . ' ' . $session->Member()->Surname : '',
                    'gamingsession_chat_id' => $chat ? $chat['id'] : '',
                    'gamingsession_chat_name' => $chat ? $chat['name'] : '',
                ], $member->FirstName . ' just cancelled attendance at '. $session->Name, 'Message to ask why?');
            }
            return (new JsonApi)->formatReturn(['status' => 'success']);
        } else {
            return $this->handleError(404, 'TO DO: Unsupported object type!');
        }
    }

    /**
     * Get All Friends
     *
     * - Get all user friends that have accepted their friend request,
     *   excluding those who have blocked the user.
     *
     * - params example:
     * ?fieldset=sessions,friends,sessioninvitations,joinrequests&search_name=snow&filter=game,location&game_name=chess,clue&location_origin=51.336036,-0.2673819999999978&distance=40000
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function getUserFriends(SS_HTTPRequest $request)
    {
        // PERMISSION:  Can only view a users profile image if logged in and requested user has not blocked current user

        if (! $request->isGET()) return $this->handleError(404, 'Must be a GET request');

        if (! $UID = $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $member = $this->checkUuidExists($UID, 'Member')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        $arr = $request->getVar('fieldset') ? explode(',', $request->getVar('fieldset')) : null;

        $searchName = $request->getVar('search_name');

        $filter = $request->getVar('filter');

        $locationOrigin = $request->getVar('location_origin') ? explode(',', $request->getVar('location_origin')) : null;

        $FriendsData = [ 'friends' => [] ];

        // Do not allow a username search and a filter.
        if($searchName && $filter){
            return $this->handleError(404, 'TO DO: You can not search via username and filter!');
        }

        $FriendsList = new ArrayList();

        // Get all friends and save to an ArrayList.
        if($friends = Friend::get()->filter(array('Status' => 'Accepted'))) {

            foreach ($friends as $friend) {

                if ($friend->InviterID == $member->ID) {

                    $invitee = Member::get()->byID($friend->InviteeID);
                    if($invitee && $member->checkIsFriend($member, $invitee)){

                        $FriendsList->push($invitee);
                    }
                }

                if ($friend->InviteeID == $member->ID) {

                    $inviter = Member::get()->byID($friend->InviterID);

                    if($inviter && $member->checkIsFriend($member, $inviter)){

                        $FriendsList->push($inviter);
                    }
                }
            }
        }

        // Check for partial username match.
        if($searchName){
            $FriendsList = $FriendsList->filter(array('Username:PartialMatch' => $searchName))->exclude('ID', CurrentUser::getUserID())->sort('Created ASC');
        }

        if($filter == 'game'){

            $gameName = $request->getVar('game_name') ? explode(',', $request->getVar('game_name')) : null;

            if($gameName){

                foreach($FriendsList as $friend){

                    foreach ($gameName as $game) {

                        if ($friend->Games()->filter(array('objectname:PartialMatch' => $game))->count() > 0) {

                            // Check if a user (current user) hasn't blocked a player (friend) and has data to return.
                            if (!($friend->checkUserIsBlockee(CurrentUser::getUser(), $friend)) && $data = $friend->getData(true, $arr)) {
                                $FriendsData['friends'][] = $data;
                            }
                        }
                    }
                }
            } else {
                return $this->handleError(404, 'TO DO: you need to specify a game name before you can filter!');
            }
        }

        if($filter == 'location'){

            if($locationOrigin && $distance = $request->getVar('distance')){

                foreach($FriendsList as $friend) {

                    if (!empty($friend->LocationLatitude || $friend->LocationLongitude)) {
                    // Distance between friends location and request origin.
                        $OriginDistance = $friend->getDistance($locationOrigin[0], $locationOrigin[1], $friend->LocationLatitude, $friend->LocationLongitude);

                        // Return friend only if friends distance is less than or equal to request distance.
                        if ($OriginDistance && ($OriginDistance <= $distance)) {

                            // Check if a user (current user) hasn't blocked a player (friend) and has data to return.
                            if (!($friend->checkUserIsBlockee(CurrentUser::getUser(), $friend)) && $data = $friend->getData(true, $arr)) {
                                $FriendsData['friends'][] = $data;
                            }
                        }
                    }
                }

            } else {
                return $this->handleError(404, 'TO DO: you need to specify a location origin and distance before you can filter!');
            }
        }

        if(!$filter) {

            foreach ($FriendsList as $friend) {

                if(!($friend->checkUserIsBlockee(CurrentUser::getUser(), $friend)) && $data = $friend->getData(true, $arr)) {
                    $FriendsData['friends'][] = $data;
                }
            }
        }

        return (new JsonApi)->formatReturn($FriendsData);
    }

    /**
     * Get User Location
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function getUserLocation(SS_HTTPRequest $request)
    {
        if (! $request->isGET()) return $this->handleError(404, 'Must be a GET request');

        if (! $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $member = $this->checkUuidExists($request->param('UID'), 'Member')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        return (new JsonApi)->formatReturn([
            'location' => $member->Location,
            'location_latitude' => $member->LocationLatitude,
            'location_longitude' => $member->LocationLongitude
        ]);
    }

    /**
     * User Owned Games Endpoint Handler
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function userOwnedGames(SS_HTTPRequest $request)
    {
        if($request->param('UID')) {

            if($request->isDELETE()) {
                return $this->deleteUserOwnedGames($request);
            }

            if($request->isGET()) {
                return $this->getUserOwnedGames($request);
            }

            if($request->isPUT()) {
                return $this->updateUserOwnedGames($request);
            }
            
            return $this->handleError(404, 'Method not allowed (GET, PUT, DELETE)');
        }
        
        return $this->handleError(404, 'You have to provide valid User ID (UID)');
    }

    /**
     * Get User Owned Games
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function getUserOwnedGames(SS_HTTPRequest $request)
    {
        if (! $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $member = $this->checkUuidExists($request->param('UID'), 'Member')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        $GamesData = [
            'games' => []
        ];

        if($games = $member->Games()->filter('Owned', true)) {

            foreach ($games as $index => $game) {
                if ($data = $game->getData($member)) {
                    
                    $GamesData['games'][] = $data;
                    
                }
            }
            
        }

        return (new JsonApi)->formatReturn($GamesData);
    }

    /**
     * Update User Owned Game
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function updateUserOwnedGames(SS_HTTPRequest $request)
    {
        if(! $member = $this->checkUuidExists($UID = $request->param('UID'), 'Member')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        if(! $this->isCurrentUser($UID)){
            return $this->handleError(404, 'TO DO: Whoops, you can not edit this user!');
        }

        $arr = $this -> getBody($request);
        
        if(isset($arr['games_list'])) {
            
            foreach($member->Games() as $game) {
                $member->Games()->add($game->ID, array('Owned' => false));
            }
            
            foreach($arr['games_list'] as $gameUID){
                
                if($game = $this->checkUuidExists($gameUID, 'Game')) {
                    
                    $member->Games()->add($game->ID, array('Owned' => true));

                }

            }

            return (new JsonApi)->formatReturn([]);
            
        } else {
            return $this->handleError(404, 'TO DO: Whoops, you have not selected any games to update!');
        }
        
    }

    /**
     * Delete User Owned Game
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function deleteUserOwnedGames(SS_HTTPRequest $request)
    {
        $UID = $request->param('UID');
        
        if(! $member = $this->checkUuidExists($UID, 'Member')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        if(! $this->isCurrentUser($UID)){
            return $this->handleError(404, 'TO DO: Whoops, you can not edit this user!');
        }
        
        $arr = $this -> getBody($request);

        if(isset($arr['games_list'])) {
            
            foreach($arr['games_list'] as $gameUID){
                
                if($game = $this->checkUuidExists($gameUID, 'Game')) {
                    
                    $member->Games()->add($game->ID, array('Owned' => false));

                }

            }

            return (new JsonApi)->formatReturn([]);
            
        } else {
            return $this->handleError(404, 'TO DO: No games to delete!');
        }

        return (new JsonApi)->formatReturn([]);
    }

    /**
     * User Played Games Endpoint Handler
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function userPlayedGames(SS_HTTPRequest $request)
    {
        if($request->param('UID')) {
            if($request->isDELETE()) {
                
                return $this->deleteUserPlayedGames($request);
            }

            if($request->isGet()) {
                return $this->getUserPlayedGames($request);
            }

            if($request->isPUT()) {
                return $this->updateUserPlayedGames($request);
            }
                   
            return $this->handleError(404, 'Must be a GET, PUT or DELETE request');    
        }
        
        return $this->handleError(404, 'You have to provide valid User ID (UID)');
    }


    /**
     * Get User Played Games
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function getUserPlayedGames(SS_HTTPRequest $request)
    {
        if (! $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $member = $this->checkUuidExists($request->param('UID'), 'Member')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        $GamesData = [
            'games' => []
        ];

        if($games = $member->Games()->filter('Played', true)) {
            foreach ($games as $index => $game) {
                if ($data = $game->getData($member)) {
                    
                    $GamesData['games'][] = $data;
                    
                }
            }
        }
        
        return (new JsonApi)->formatReturn($GamesData);
    }

    /**
     * Update User Played Game
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function updateUserPlayedGames(SS_HTTPRequest $request)
    {
        if (! $UID = $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $member = $this->checkUuidExists($UID, 'Member')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        if(! $this->isCurrentUser($UID)){
            return $this->handleError(404, 'TO DO: Whoops, you can not edit this user!');
        }
        
        $arr = $this -> getBody($request);
        
        if(isset($arr['games_list'])) {
            
            foreach($member->Games() as $game) {
                $member->Games()->add($game->ID, array('Played' => false, 'Owned' => false));
            }
            
            foreach($arr['games_list'] as $gameUID){

                if($game = $this->checkUuidExists($gameUID, 'Game')) {
                    
                    $member->Games()->add($game->ID, array('Played' => true));

                }

            }

            return (new JsonApi)->formatReturn([]);
            
        } else {
            return $this->handleError(404, 'TO DO: Whoops, you have not selected any games to update!');
        }

    }

    /**
     * Delete User Played Game
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function deleteUserPlayedGames(SS_HTTPRequest $request)
    {
        if (! $UID = $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $member = $this->checkUuidExists($UID, 'Member')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        if(! $this->isCurrentUser($UID)){
            return $this->handleError(404, 'TO DO: Whoops, you can not edit this user!');
        }

        $arr = $this -> getBody($request);

        if(isset($arr['games_list'])) {
            
            foreach($arr['games_list'] as $gameUID){
                
                if($game = $this->checkUuidExists($gameUID, 'Game')) {
                    
                    $member->Games()->add($game->ID, array('Played' => false, 'Owned' => false));

                }

            }

            return (new JsonApi)->formatReturn([]);
            
        } else {
            return $this->handleError(404, 'TO DO: No games to delete!');
        }

        return (new JsonApi)->formatReturn([]);
    }

    /**
     * User Badges Endpoint Handler
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function userBadges(SS_HTTPRequest $request)
    {
        if(! $request->param('badgeUID')){

            if (! $request->isGET()) return $this->handleError(404, 'Must be a GET request');

            if($request->isGET()) {
                return $this->getUserBadges($request);
            }

        } else {

            if (!($request->isPUT() || $request->isDELETE())) return $this->handleError(404, 'Must be a PUT or DELETE request');

            if($request->isPUT()) {
                return $this->updateUserBadges($request);
            }

            if($request->isDELETE()) {
                return $this->deleteUserBadges($request);
            }
        }
    }

    /**
     * Get User Badges
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function getUserBadges(SS_HTTPRequest $request)
    {
        if (! $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $member = $this->checkUuidExists($request->param('UID'), 'Member')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        $BadgesData = [
            'badges' => []
        ];

        if($badges = $member->Badges()) {
            foreach ($badges as $badge) {
                if ($data = $badge->getData()) {
                    $BadgesData['badges'][] = $data;
                }
            }
        }

        return (new JsonApi)->formatReturn($BadgesData);
    }

    /**
     * Update User Badges
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function updateUserBadges(SS_HTTPRequest $request)
    {
        if (! $UID = $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $member = $this->checkUuidExists($UID, 'Member')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        if(! $this->isCurrentUser($UID)){
            return $this->handleError(404, 'TO DO: Whoops, you can not edit this user!');
        }

        if(! $badge = $this->checkUuidExists($request->param('badgeUID'), 'Badge')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        $member->Badges()->add($badge);

        return (new JsonApi)->formatReturn([]);
    }

    /**
     * Delete User Badges
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function deleteUserBadges(SS_HTTPRequest $request)
    {
        if (! $UID = $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $member = $this->checkUuidExists($UID, 'Member')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        if(! $this->isCurrentUser($UID)){
            return $this->handleError(404, 'TO DO: Whoops, you can not edit this user!');
        }

        if(! $badge = $this->checkUuidExists($request->param('badgeUID'), 'Badge')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        $member->Badges()->remove($badge);

        return (new JsonApi)->formatReturn([]);
    }

    /**
     * Send Friend Request
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function userFriendRequest(SS_HTTPRequest $request)
    {
        if (! $request->isPUT()) return $this->handleError(404, 'Must be a PUT request');

        if (!($UID = $request->param('UID')) || !($friendUID = $request->param('friendUID'))) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if($friendUID == $UID){
            return $this->handleError(404, 'TO DO: You can not send a friend request to yourself! - duh');
        }

        if(!($member = $this->checkUuidExists($request->param('UID'), 'Member')) || !($friend = $this->checkUuidExists($request->param('friendUID'), 'Member'))) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }
        
        

        $f = Friend::get()->filter(array('InviterID' => $member->ID, 'InviteeID' => $friend->ID))->sort('Created DESC')->first();

        if ($f && ($f->Status == 'Pending')) {

            return $this->handleError(409, 'Friend request already sent to this person.', 409);

        } else if ($f && ($f->Status == 'Accepted')) {

            return $this->handleError(404, 'This person is already your friend.');

        } 

        $f = Friend::get()->filter(array('InviteeID' => $member->ID, 'InviterID' => $friend->ID))->sort('Created DESC')->first();

        if($f && ($f->Status == 'Pending')) {

            return $this->handleError(408, 'You have a friend request pending for this user, please answer it! or something', 408);

        } else if($f && ($f->Status == 'Accepted')) {

            return $this->handleError(404, 'This person is already your friend.');

        } 

        $newFriend = Friend::create();
        $newFriend->Status = 'Pending';
        $newFriend->InviterID = $member->ID;
        $newFriend->InviteeID = $friend->ID;
        $newFriend->write();
        
        if(!empty($friend->PushToken)) {
            $push = new Fcm;
            $push->send($friend->PushToken, 'friend', ['friend_uid' => $member->UUID, 'friend_name' =>  $member->FirstName.' '.$member->Surname],  $member->FirstName.' sent you a friend request.');
        }

        return (new JsonApi)->formatReturn([]);
    }
    
    /**
     * Send Batch Friend Request
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function userFriendBatchRequest(SS_HTTPRequest $request)
    {
        if (! $request->isPUT()) return $this->handleError(404, 'Must be a PUT request');

        if (!($UID = $request->param('UID'))) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }
        if(!($member = $this->checkUuidExists($request->param('UID'), 'Member'))) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }
        
        $data = $this->getBody($request);
        
        $requests = 0;
        $sent = 0;
        $pushTokens = [];
        
        if(!empty($data['requests'])) {
            $requests = count($data['requests']);
            
            foreach($data['requests'] as $friendUID) {
                $friend = $this->checkUuidExists($friendUID, 'Member');
                
                if($friend && ($friendUID != $UID) ) {
                    $skip = false;
                    // this loops should return nothing - just a precausion
                    $f = Friend::get()->filter(array('InviterID' => $member->ID, 'InviteeID' => $friend->ID))->sort('Created DESC')->first();

                    if ($f && ($f->Status == 'Pending') || ($f->Status == 'Accepted')) {
                        $skip = true;
                    } 

                    if(!$skip) {
                        $f = Friend::get()->filter(array('InviteeID' => $member->ID, 'InviterID' => $friend->ID))->sort('Created DESC')->first();

                        if($f && ($f->Status == 'Pending') || ($f->Status == 'Accepted')) {
                            $skip = true;
                        } 
                        
                        if(!$skip) {
                            $newFriend = Friend::create();
                            $newFriend->Status = 'Pending';
                            $newFriend->InviterID = $member->ID;
                            $newFriend->InviteeID = $friend->ID;
                            $newFriend->write();
                            
                            if(!empty($friend->PushToken)){
                                $pushTokens[] = $friend->PushToken;
                            }
                            
                            $sent++;
                        }
                    }
                }
            }
            
        }
        
        if(!empty($pushTokens)){
            $push = new Fcm;
            $push->send($pushTokens, 'friend', ['friend_uid' => $member->UUID, 'friend_name' =>  $member->FirstName.' '.$member->Surname],  $member->FirstName.' sent you a friend request.', '',true);
        }

        return (new JsonApi)->formatReturn(['requests' => $requests, 'sent' => $sent]);
    }

    /**
     * Accept Friend Request
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function userAcceptFriendRequest(SS_HTTPRequest $request)
    {
        if (! $request->isPUT()) return $this->handleError(404, 'Must be a PUT request');

        if (!($UID = $request->param('UID')) || !($friendUID = $request->param('friendUID'))) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if (! $friendUID) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if($friendUID == $UID){
            return $this->handleError(404, 'TO DO: You can not accept your own friend request! - duh');
        }

        if(!($member = $this->checkUuidExists($request->param('UID'), 'Member')) || !($friend = $this->checkUuidExists($request->param('friendUID'), 'Member'))) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        if($request = Friend::get()->filter(array('Status' => 'Pending', 'InviterID' => $friend->ID, 'InviteeID' => $member->ID))->sort('Created DESC')->first()){
            $request->Status = 'Accepted';
            $request->write();

            return (new JsonApi)->formatReturn([]);
        } else {

            return $this->handleError(404, 'TO DO: You do not have a friend request from this player!');
        }


    }

    /**
     * Reject Friend Request
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function userRejectFriendRequest(SS_HTTPRequest $request)
    {
        if (! $request->isPUT()) return $this->handleError(404, 'Must be a PUT request');

        if (!($UID = $request->param('UID')) || !($friendUID = $request->param('friendUID'))) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if (! $friendUID) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if($friendUID == $UID){
            return $this->handleError(404, 'TO DO: You can not reject your own friend request! - duh');
        }

        if(!($member = $this->checkUuidExists($request->param('UID'), 'Member')) || !($friend = $this->checkUuidExists($request->param('friendUID'), 'Member'))) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        if($request = Friend::get()->filter(array('Status' => 'Pending', 'InviterID' => $friend->ID, 'InviteeID' => $member->ID))->sort('Created DESC')->first()){
            $request->Status = 'Rejected';
            $request->write();

            return (new JsonApi)->formatReturn([]);
        } else {

            return $this->handleError(404, 'TO DO: You do not have a friend request from this player!');
        }


    }


    /**
     * Block Specific User
     * - block any user, provided they are not the current user or are already blocked.
     * - if users are friends, set their status to blocked.
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function userBlock(SS_HTTPRequest $request)
    {
        if (!$request->isPOST()) return $this->handleError(404, 'Must be a POST request');

        if (!($UID = $request->param('UID')) || !($blockeeUID = $request->param('blockeduserUID'))) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if ($UID != CurrentUser::getUserUUID()) {
            return $this->handleError(404, 'TO DO: Your user does not match the currently logged in user!');
        }

        if ($blockeeUID == $UID) {
            return $this->handleError(404, 'TO DO: You can not block yourself! - duh');
        }

        if (!($blocker = $this->checkUuidExists($request->param('UID'), 'Member')) || !($blockee = $this->checkUuidExists($request->param('blockeduserUID'), 'Member'))) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        if($blocker->checkUserIsBlocker($blocker, $blockee)) {
            return $this->handleError(404, 'TO DO: Already blocked this user!');
        }

        if($blocker->checkIsFriend($blocker, $blockee)) {

            if ($friends = Friend::get()->filter(array('Status' => 'Accepted'))) {

                foreach ($friends as $friend) {

                    if ($friend->InviterID == $blocker->ID && $friend->InviteeID == $blockee->ID) {

                        $friend->Status = 'Blocked';
                        $friend->write();
                    }

                    if ($friend->InviterID == $blockee->ID && $friend->InviteeID == $blocker->ID) {

                        $friend->Status = 'Blocked';
                        $friend->write();
                    }
                }
            }
        }

        $BlockedUser = BlockedUser::create();
        $BlockedUser->Blocked = true;
        $BlockedUser->BlockerID = $blocker->ID;
        $BlockedUser->BlockeeID = $blockee->ID;
        $BlockedUser->write();

        return (new JsonApi)->formatReturn([]);
    }
    
    /**
     * Unblock  Users
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function usersUnblock(SS_HTTPRequest $request)
    {
        if (!$request->isPOST()) return $this->handleError(404, 'Must be a POST request');
        
        if (!$UID = $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if ($UID != CurrentUser::getUserUUID()) {
            return $this->handleError(404, 'TO DO: Your user does not match the currently logged in user!');
        }

        if (!$unblocker = $this->checkUuidExists($request->param('UID'), 'Member')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }
        
        $data = $this->getBody($request);
        
        if(isset($data['users']) && is_array($data['users'])) {
            
            foreach($data['users'] as $user) {
                if($blocked = $this->checkUuidExists($user, 'Member')) {
                
                    if($unblocker->checkUserIsBlocker($unblocker, $blocked)) {

                        if ($friends = Friend::get()->filter(array('Status' => 'Blocked'))) {

                            foreach ($friends as $friend) {

                                if ($friend->InviterID == $unblocker->ID && $friend->InviteeID == $blocked->ID) {

                                    $friend->Status = 'Accepted';
                                    $friend->write();
                                }

                                if ($friend->InviterID == $blocked->ID && $friend->InviteeID == $unblocker->ID) {

                                    $friend->Status = 'Accepted';
                                    $friend->write();
                                }
                            }
                        }

                        BlockedUser::get()->filter(['BlockerID' => $unblocker->ID, 'BlockeeID' => $blocked->ID])->removeAll();

                    }
                }
            }
        } else {
            return $this->handleError(404, 'TO DO: Pass users to unblock s array!');
        }

        return (new JsonApi)->formatReturn([]);
    }

    /**
     * Get Specific User Blocked List
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function getBlockedUsers(SS_HTTPRequest $request)
    {
        if (!$request->isGET()) return $this->handleError(404, 'Must be a GET request');

        if (!($UID = $request->param('UID'))) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if ($UID != CurrentUser::getUserUUID()) {
            return $this->handleError(404, 'TO DO: Your user does not match the currently logged in user!');
        }

        if(! $member = $this->checkUuidExists($request->param('UID'), 'Member')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        $BlockedUsersData = [
            'users' => []
        ];

        if($BlockedUsers = BlockedUser::get()->filter('BlockerID', $member->ID)){

            foreach($BlockedUsers as $blockedUser){

                $user = Member::get()->byID($blockedUser->BlockeeID);

                if($user && $user->getData()){

                    $BlockedUsersData['users'][] = $user->getData();
                }
            }
        }

        return (new JsonApi)->formatReturn($BlockedUsersData);
    }

    /**
     * Report Specific User
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function userReport(SS_HTTPRequest $request)
    {
        if (!$request->isPOST()) return $this->handleError(404, 'Must be a POST request');

        if (!($UID = $request->param('UID')) || !($reporteeUID = $request->param('reporteduserUID'))) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if (!$reporteeUID) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if ($UID != CurrentUser::getUserUUID()) {
            return $this->handleError(404, 'TO DO: Your user does not match the currently logged in user!');
        }

        if ($reporteeUID == $UID) {
            return $this->handleError(404, 'TO DO: You can not report yourself! - duh');
        }

        if (!($reporter = $this->checkUuidExists($request->param('UID'), 'Member')) || !($reportee = $this->checkUuidExists($request->param('reporteduserUID'), 'Member'))) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        if($ReportedUser = ReportUser::get()->filter(array('ReporterID' => $reporter->ID, 'ReporteeID' => $reportee->ID))->first()){

            return $this->handleError(409, 'TO DO: You have already reported this user!');

        } else {

            $message = $this -> getBody($request)['message'];
            
            $ReportedUser = ReportUser::create();
            $ReportedUser->Reported = true;
            $ReportedUser->ReporterID = $reporter->ID;
            $ReportedUser->ReporteeID = $reportee->ID;
            $ReportedUser -> Message = $message;
            $ReportedUser->write();

            $SiteConfig = SiteConfig::current_site_config();

            $search = array(
                "%%USERNAME%%" => $reportee->Username,
                "%%EMAIL%%" => $reportee->Email,
                "%%MESSAGE%%" => $message
            );

            $body = str_replace(array_keys($search), array_values($search), $SiteConfig->ReportEmailContent);

            $email = new Email();
            $email
                ->setFrom($SiteConfig->ReportEmailFrom)
                ->setTo($SiteConfig->ReportEmailTo)
                ->setSubject($SiteConfig->ReportEmailSubject)
                ->setTemplate('ReportEmail')
                ->populateTemplate(array(
                    'Content' => $body
                ));

            $email->send();

            return (new JsonApi)->formatReturn([]);
        }
    }
    
    public function deleteUser(SS_HTTPRequest $request) {
        if (! $UID = $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $member = $this->checkUuidExists($UID, 'Member')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        if(! $this->isCurrentUser($UID)){
            return $this->handleError(404, 'TO DO: Whoops, you can not edit this user!');
        }

        try {
            $member->UserStatus = 'Deleted';
            
            $InvitationSenders = $member->InvitationSenders()->filter(['Status' => 'Pending']);
            foreach($InvitationSenders as $InvitationSender) {
                $InvitationSender->Status = 'Cancelled';
                $InvitationSender->write();
            }
            
            $InvitationRecipients = $member->InvitationRecipients()->filter(['Status' => 'Pending']);
            foreach($InvitationRecipients as $InvitationRecipient) {
                $InvitationRecipient->Status = 'Rejected';
                $InvitationRecipient->write();
            }
            
            $RequestSenders = $member->RequestSenders()->filter(['Status' => 'Pending']);
            foreach($RequestSenders as $RequestSender) {
                $RequestSender->Status = 'Cancelled';
                $RequestSender->write();
            }
            
            $RequestRecipients = $member->RequestRecipients()->filter(['Status' => 'Pending']);
            foreach($RequestRecipients as $RequestRecipient) {
                $RequestRecipient->Status = 'Rejected';
                $RequestRecipient->write();
            }
            
            $member->write();
        } catch (Exception $e) {
            return $this->handleError(5000, $e->getMessage(), 400);
        }

        return (new JsonApi)->formatReturn(['user' => $member->getData()]);
    }
    
    /**
     * Delete Friend
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function userDeleteFriend(SS_HTTPRequest $request)
    {
        if (! $request->isDelete()) return $this->handleError(404, 'Must be a DELETE request');

        if (!($UID = $request->param('UID')) || !($friendUID = $request->param('friendUID'))) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(!($member = $this->checkUuidExists($request->param('UID'), 'Member')) || !($friend = $this->checkUuidExists($request->param('friendUID'), 'Member'))) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }
        $friend =  Friend::get()->filter(['InviterID' => $friend->ID, 'InviteeID' => $member->ID])->first();
        
        if($friend && ($friend->Status == 'Accepted')) {
            
            $friend->Status = 'Deleted';
            $friend->write();
            
            return (new JsonApi)->formatReturn([]);
            
        } else {
            
            $friend =  Friend::get()->filter(['InviteeID' => $friend->ID, 'InviterID' => $member->ID])->first();
            
             if($friend && ($friend->Status == 'Accepted')) {
                 
                $friend->Status = 'Deleted';
                $friend->write();
                
                return (new JsonApi)->formatReturn([]);
                
             } else {
                 
                return $this->handleError(404, 'TO DO: You are not a friend with this player!');
                
             }
        }

    }
    
    /**
     * Get Potential Friends according to FB
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function getUserPotentialFriends(SS_HTTPRequest $request)
    {
        if (! $request->isGET()) return $this->handleError(404, 'Must be a GET request');

        if (! $UID = $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }
        
        if (! $accessToken = $request->param('accessToken')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $member = $this->checkUuidExists($UID, 'Member')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }
        
        $potentialFirendsData = ['friends' => []];
        
        $facebook = new FacebookPlaylist($accessToken);
        $result = $facebook->getFriends($member->FacebookUserID);
        
        if(isset($result['data'])) {

            foreach($result['data'] as $fbFriend) {
                
                $potentialFriend = Member::get()->filter(['FacebookUserID' => $fbFriend['id']])->first();
                
                if($potentialFriend && !$member->checkIsFriend($member, $potentialFriend)) {
                    
                    $potentialFirendsData['friends'][] = $potentialFriend->getData();
                }
            }
        }

        return (new JsonApi)->formatReturn($potentialFirendsData);
    }
    
    public function pushToken(SS_HTTPRequest $request) {
         if (! $request->isPost()) return $this->handleError(404, 'Must be a POST request');
         
         $data = $this->getBody($request);
         
         if(isset($data['push_token'])) {
             //find token
             $member = Member::get()->filter(['PushToken' => $data['push_token']]);
             if($member->exists()) {
             //clear current device user
                 $member = $member->first();
                 $member->PushToken = null;
                 $member->write();
             }
             //add to new user
            $user = Member::get()->byID(CurrentUser::getUserID());
            $user->PushToken = $data['push_token'];
            if($user->write()) {
                return (new JsonApi)->formatReturn([]);
            }
         }
         
         return $this->handleError(404, 'You must specify a push token!');
    }
    
    public function getUserNotifications(SS_HTTPRequest $request) {

        if (! $request->isGET()) return $this->handleError(404, 'Must be a GET request');

        if (! $UID = $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }
        
        if ($UID != CurrentUser::getUserUUID()) {
            return $this->handleError(404, 'TO DO: Your user does not match the currently logged in user!');
        }
        
        if(! $member = $this->checkUuidExists($UID, 'Member')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }
        
        $notificationsObj = $member->Notifications()->filter(['Dismissed' => 0]);
        $notifiations = [];
        
        foreach($notificationsObj as $notificationObj) {
            if($this->checkRelatedObject($notificationObj)) {
                $notifiations[] = $notificationObj->getData();
            } else {
                $notificationObj->Dismissed = 1;
                $notificationObj->write();
            }
        }
        
        return (new JsonApi)->formatReturn([ 'notifications' => $notifiations ]);
        
    }
    
    public function getUserNotification(SS_HTTPRequest $request) {
        
        if (! $request->isGET()) return $this->handleError(404, 'Must be a GET request');

        if (! $UID = $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }
        
        if (! $notificationUID = $request->param('notificationUID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }
        
        if ($UID != CurrentUser::getUserUUID()) {
            return $this->handleError(404, 'TO DO: Your user does not match the currently logged in user!');
        }
        
        if(! $member = $this->checkUuidExists($UID, 'Member')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }
        
        $notificationsObj = $member->Notifications()->filter(['UUID' => $notificationUID, 'Dismissed' => 0])->first();
        
        return (new JsonApi)->formatReturn(['notification' => $notificationsObj ? $notificationsObj->getData() : false ]);
        
    }
    
    
    public function dismissUserNotification(SS_HTTPRequest $request) {
        
        if (! $request->isPUT()) return $this->handleError(404, 'Must be a PUT request');

        if (! $UID = $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }
        
        if (! $notificationUID = $request->param('notificationUID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }
        
        if ($UID != CurrentUser::getUserUUID()) {
            return $this->handleError(404, 'TO DO: Your user does not match the currently logged in user!');
        }
        
        if(! $member = $this->checkUuidExists($UID, 'Member')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }
        
        $notificationsObj = $member->Notifications()->filter(['UUID' => $notificationUID])->first();
        
        if($notificationsObj) {
            
            $notificationsObj->Dismissed = 1;
            
            if($notificationsObj->write()) {
                return (new JsonApi)->formatReturn([]);
                
            } else {
                return $this->handleError(404, 'TO DO: Db error while saving!');
            }
            
        } else {
            return $this->handleError(404, 'TO DO: No such notification!');
        }
    }
    
    public function contactUs(SS_HTTPRequest $request) {
         if (! $request->isPost()) return $this->handleError(404, 'Must be a POST request');
         
         $SiteConfig = SiteConfig::current_site_config();
         
         $data = $this->getBody($request);
         
         if(isset($data['message'])) {
            
            $user = CurrentUser::getUser();
                    
                $emailObj = new MusterEmail;
                $emailObj->SenderID = $user->ID;
                $emailObj->ReceiverID = 0;
                $emailObj->Message = $data['message'];
                $emailObj->Type = 'ContactUs';
                $emailObj->Sent = 0;
                
                if(!$emailObj->write()) {
                    
                     return $this->handleError(404, 'Cannot send message, try again!');
                     
                } else {
                    
                    $email = new Email();
                    $email
                        ->setFrom($SiteConfig->ContactUsEmailTo)
                        ->setTo($SiteConfig->ContactUsEmailTo)
                        ->setSubject($SiteConfig->ContactUsSubject . ' ' . $user->FirstName . ' ' . $user->Surname)
                        ->setBody($data['message']);
                    
                    $emailObj->Sent = !empty($email->send());
                    $emailObj->write();
                    
                    return (new JsonApi)->formatReturn(['sent' => $emailObj->Sent]);
                }
                
         }
         
         return $this->handleError(404, 'You must specify message!');
    }
    
    public function requestlgs(SS_HTTPRequest $request) {
        if (! $request->isPost()) return $this->handleError(404, 'Must be a POST request');
         
        $SiteConfig = SiteConfig::current_site_config();
        
        $data = $this->getBody($request);
         
            
        $user = CurrentUser::getUser();

            $emailObj = new MusterEmail;
            $emailObj->SenderID = $user->ID;
            $emailObj->ReceiverID = 0;
            $emailObj->Message = isset($data['message']) ? $data['message'] : '';
            $emailObj->Type = 'RequestLGS';
            $emailObj->Sent = 0;

            if(!$emailObj->write()) {

                 return $this->handleError(404, 'Cannot send message, try again!');

            } else {

                $email = new Email();
                $email
                    ->setFrom($SiteConfig->LGSEmailTo)
                    ->setTo($SiteConfig->LGSEmailTo)
                    ->setSubject($SiteConfig->LGSSubject . ' - ' . $user->FirstName . ' ' . $user->Surname)
                    ->setBody($data['message']);

                $emailObj->Sent = !empty($email->send());
                $emailObj->write();

                return (new JsonApi)->formatReturn(['sent' => $emailObj->Sent]);
            }
                
         
         return $this->handleError(404, 'You must specify message!');
    }
    
    public function hide(SS_HTTPRequest $request) {
        if (! $request->isPost()) return $this->handleError(404, 'Must be a POST request');
        
        $user = CurrentUser::getUser();
        
        $user->Hidden = true;
        if($user->write()) {
            
            return (new JsonApi)->formatReturn(['hidden' => true]);
            
        } else {
            
           return $this->handleError(404, 'Cannot hide user, try again!'); 
            
        }

    }
    
    public function unhide(SS_HTTPRequest $request) {
        if (! $request->isPost()) return $this->handleError(404, 'Must be a POST request');
        
        $user = CurrentUser::getUser();
        
        $user->Hidden = false;
        if($user->write()) {
            
            return (new JsonApi)->formatReturn(['hidden' => false]);
            
        } else {
            
           return $this->handleError(404, 'Cannot unhide user, try again!'); 
            
        }

    }
    
    public function setFacebookPhoto(SS_HTTPRequest $request) {
        if (! $request->isPost()) return $this->handleError(404, 'Must be a POST request');
        
        $user = CurrentUser::getUser();
        
        if($user->FacebookUserID) {
            
            $image = $user->getFbImage();
            
            return (new JsonApi)->formatReturn(['image' => $image]);
            
        } else {
            
           return $this->handleError(404, 'Cannot set facebook photo, no facebook id for user!'); 
            
        }

    }
    
    protected function checkRelatedObject($notificationObj) {
        $data = json_decode($notificationObj->StoredData, true);
        
        switch($notificationObj->Type) {
            case 'gamingsessioninvitation':
            case 'gamingsessioninvitation_response':
                return GamingSession::get()->filter(['UUID' => $data['gamingsession_uid']])->exists() && GamingSessionInvitation::get()->filter(['UUID' => $data['invitation_uid']])->exists();
            case 'gamingsession':
            case 'gamingsessionresult':
            case 'gamingsession_friendnearby':
                return GamingSession::get()->filter(['UUID' => $data['gamingsession_uid']])->exists();
            case 'joinrequest':
            case 'joinrequest_response':
                return GamingSession::get()->filter(['UUID' => $data['gamingsession_uid']])->exists() && GamingSessionJoinRequest::get()->filter(['UUID' => $data['joinrequest_uid']])->exists();
            case 'friend':
            case 'message_request':
            case 'locationchange':
                return Member::get()->filter(['UUID' => $data['friend_uid']])->exists();
            case 'gamingsessiondaily':
            case 'join_info':
            default:
                return true;
        }
    }
}
