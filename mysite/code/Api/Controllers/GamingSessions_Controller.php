<?php

use Respect\Validation\Validator as v;

class GamingSessions_Controller extends Api_Controller {

    protected $auth = true;

    private static $allowed_actions = array(
        'handler',
        'gamingSessionImage',
        'gamingSessionInvitations',
        'gamingSessionRequests',
        'gamingSessionResult',
        'gamingSessionAttendance',
        'gamingSessionRematch',
        'uidHandler',
    );

    private static $url_handlers = array(
        '' => 'handler',                                                                               // GET, POST
        '$UID/image' => 'gamingSessionImage',                                                          // GET, POST
        '$UID/sessioninvitations/$invitationUID/$status' => 'gamingSessionInvitations',                // GET, POST, PUT
        '$UID/joinrequests/$joinrequestUID/$status' => 'gamingSessionRequests',                        // GET, POST, PUT
        '$UID/result/$userUID' => 'gamingSessionResult',                                               // GET, POST, PUT
        '$UID/rematch' => 'gamingSessionRematch',                                                      // POST
        '$UID/sessionattendance/$attendanceUID' => 'gamingSessionAttendance',                          // DELETE
        '$UID' => 'uidHandler',                                                                        // GET, POST, DELETE
    );

    /**
     * Gaming Session Handler
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function handler(SS_HTTPRequest $request)
    {
        if (!($request->isGet() || $request->isPOST())) return $this->handleError(404, 'Must be a GET or POST request');

        if($request->isGET()) {
            return $this->getAllGamingSessions($request);
        }

        if($request->isPOST()) {
            return $this->createGamingSession($request);
        }
    }

    /**
     * Get All Gaming Sessions
     *
     * @param SS_HTTPRequest $request
     * @return SS_HTTPResponse
     */
    public function getAllGamingSessions(SS_HTTPRequest $request)
    {
        $gameName = $request->getVar('search_game');
        $games = $request->getVar('game_uuid');
        
        $gamingSessions = GamingSession::get()->sort('Created ASC');
        
        $GamingSessionsData = [
            'gaming_sessions' => []
        ];

        if($gameName || $games)
        {
            $gamingSessions = $gamingSessions->innerJoin("Game", "Game.ID = GamingSession.GameID");
            
            if($games) {
                
                $gamingSessions = $gamingSessions->where('Game.UUID in (\''.implode('\',\'', explode(',',$games)).'\')');
            }
            
            if($gameName) {
                
                $gamingSessions = $gamingSessions -> filterAny(array(
                    'objectname:PartialMatch' => $gameName,
                    'originalname:PartialMatch' => $gameName,
                ));
                
            }
        }

        $gamingSessions = $gamingSessions -> filter(['StartDate:GreaterThan' => date('U'), 'GameStatus' => 'Active']);
        
        if(($locationOrigin = $request->getVar('location_origin') ? explode(',', $request->getVar('location_origin')) : null) && $distance = $request->getVar('distance'))
        {
            $checkLocation = true;
            
        } else {
            
            $checkLocation = false;
            
        }

        foreach ($gamingSessions as $gamingSession) {
             
            $data = $gamingSession->getData();

            if($data['hidden'] == 1 && $data['host']['friend_with_currend_user'] == 0 && CurrentUser::getUserID() != $data['host']['chat_id'] )
            {
                continue;
            }
                        
            if ($checkLocation && ($gamingSession->Venue() -> ID || $gamingSession->Location() -> ID)) {

                $place = $gamingSession->Venue() -> ID ? $gamingSession->Venue() : $gamingSession->Location();
              
                $OriginDistance = $gamingSession->getDistance($locationOrigin[0], $locationOrigin[1], $place->LocationLatitude, $place->LocationLongitude);

                if (isset($OriginDistance) && ($OriginDistance <= $distance)) {

                    if($data = $gamingSession->getData()) {
                                            
                        $GamingSessionsData['gaming_sessions'][] = $data;
                        
                    }
                }
                
            } else {

                if($data = $gamingSession->getData()) {
                    
                    $GamingSessionsData['gaming_sessions'][] = $data;
                    
                }

            }
        } 
        return (new JsonApi)->formatReturn($GamingSessionsData);
    }

    /**
     * Create Gaming Session
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function createGamingSession(SS_HTTPRequest $request)
    {
        // TO DO: Make some fields compulsory!!
        // TO DO: Make sure host_UID is logged in user or remove it.
        // TO DO: Verify venue and game exist
        $GamingSession = GamingSession::create();

        $data = $request->postVars();

        $privacyStatus = DB::get_schema()->enumValuesForField(new GamingSession(), 'PrivacySetting');
        
        if(!$this->checkMultipartContentType($request->getHeader('Content-Type'))) {
            return $this->handleError(2003);
        }

        if (isset($data['name']) && v::stringType()->notEmpty()->validate($data['name'])) {
            $GamingSession->Name = $data['name'];
        }

        if (isset($data['description']) && v::stringType()->validate($data['description'])) {
            $GamingSession->Description = $data['description'];
        }

        if (isset($data['start_date']) && v::numeric()->notEmpty()->validate($data['start_date'])) {
            $GamingSession->StartDate = date('Y-m-d H:i:s', $data['start_date']);
        }

        if (isset($data['end_date']) && v::numeric()->notEmpty()->validate($data['end_date'])) {
            $GamingSession->EndDate = date('Y-m-d H:i:s', $data['end_date']);
        }

        if (isset($data['player_limit']) && v::numeric()->validate($data['player_limit'])) {
            $GamingSession->PlayerLimit = $data['player_limit'];
            
        } else {
            $GamingSession->PlayerLimit = -1;
        }

        if (isset($data['game_status']) && v::stringType()->validate($data['game_status'])) {
            $GamingSession->GameStatus = $data['game_status'];
        }

        if (isset($data['privacy_setting']) && v::stringType()->in($privacyStatus)->validate($data['privacy_setting'])) {
            $GamingSession->PrivacySetting = $data['privacy_setting'];
        }

        if (isset($data['hidden']) && v::boolType()->validate($data['hidden'])) {
            $GamingSession->Hidden = $data['hidden'];
        }
        
        if (isset($data['recurring']) && v::stringType()->in(DB::get_schema()->enumValuesForField(new GamingSession, 'Recurring'))->validate($data['recurring'])) {
            $GamingSession->Recurring = $data['recurring'];
        }
        
        if (isset($data['venue_uid']) && v::stringType()->validate($data['venue_uid'])) {

            if($Venue = $this->checkUuidExists($data['venue_uid'], 'Venue')) {
                $GamingSession->VenueID = $Venue->ID;
            } else {
                return $this->handleError(404, 'TO DO: Venue UID wrong!');
            }
        } else {
            
            if (
                    isset($data['location']) && v::stringType()->validate($data['location']) &&
                    isset($data['location_latitude']) && v::numeric()->validate($data['location_latitude']) &&
                    isset($data['location_longitude']) && v::numeric()->validate($data['location_longitude'])
                    ) {
                
                $Location = Location::get()->filter(['Location' => $data['location'], 'LocationLatitude' => $data['location_latitude'], 'LocationLongitude' => $data['location_longitude']])->first();
                
                if(!$Location) {
                    
                    $Location = new Location;
                    $Location->Location = $data['location'];
                    $Location->LocationLatitude = $data['location_latitude'];
                    $Location->LocationLongitude = $data['location_longitude'];
                    
                    try {
                        $Location->write();
                    } catch (Exception $e) {
                        return $this->handleError(5000, $e->getMessage(), 400);
                    }
                }
                $GamingSession->LocationID = $Location->ID;
                
            } else {
                
                return $this->handleError(404, 'TO DO: Pass Venue UID or valid Location parameters');
                
            }
            
        }

        if (isset($data['game_uid']) && v::stringType()->notEmpty()->validate($data['game_uid'])) {

            if($Game = $this->checkUuidExists($data['game_uid'], 'Game')) {
                $GamingSession->GameID = $Game->ID;
            } else {
                return $this->handleError(404, 'TO DO: Game UID wrong!');
            }
        }

        $GamingSession->MemberID = Member::get()->filter('ID', CurrentUser::getUserID())->first()->ID;

        try {
            
            // CREATE CHAT
//            $GamingSession->ChatUUID = CometchatHelper::addChatroom($GamingSession->MemberID, $GamingSession->Name .' '. $GamingSession->StartDate);
//            $chat = DB::prepared_query('SELECT * FROM "cometchat_chatrooms" WHERE "cometchat_chatrooms"."guid" = ?', array($GamingSession->ChatUUID))->first();
            $chat = null;
            //ADD OWNER TO CHAT
//            CometchatHelper::addToChatroom($GamingSession->ChatUUID, $GamingSession->MemberID);
            
            $GamingSession->write();
            
            if(isset($_FILES['image'])) {
 
                $image = $this->performUpload($_FILES['image'], $GamingSession, 'GamingSessions');

                if (! empty($image)) {
                    $GamingSession->GamingSessionImageID = $image->ID;
                    $GamingSession->write();
                }
            
            } 
            
            CurrentUser::getUser()->GamingSessions()->add($GamingSession->ID);

            if (isset($Location)) {
                $Location->GamingSessions()->add($GamingSession->ID);
            }
            
            if(isset($Venue)) {
                $Venue->GamingSessions()->add($GamingSession->ID);
            }

            if(isset($Game)) {
                $Game->GamingSessions()->add($GamingSession->ID);
            }

            
        } catch (Exception $e) {
            return $this->handleError(5000, $e->getMessage(), 400);
        }
        
        $requested = 0;
        $sent = 0;
        
        if (isset($data['invitations']) && is_array($data['invitations'])) {
            
            $data['invitations'] = array_unique($data['invitations']);
            
            $requested = count($data['invitations']);
            
            foreach($data['invitations'] as $invitation) {
                
                if($Member = $this->checkUuidExists($invitation, 'Member')) {
                    
                    if($Member->ID != CurrentUser::getUserID()){
                        
                        $Invitation = GamingSessionInvitation::create();
                        $Invitation->Status = 'Pending';
                        $Invitation->GamingSessionID = $GamingSession->ID;
                        $Invitation->InvitationSenderID = CurrentUser::getUserID();
                        $Invitation->InvitationRecipientID = $Member->ID;
                        if ($Invitation->write()) {
                            $sent++;
                            if(!empty($Member->PushToken)) {
                                $push = new Fcm;
                                $push->send($Member->PushToken, 'gamingsessioninvitation', [ 
                                    'gamingsession_uid' => $GamingSession->UUID, 
                                    'invitation_uid' => $Invitation->UUID,
                                    'gamingsession_start_date' => (int)$GamingSession->dbObject('StartDate')->format('U'), 
                                    'gamingsession_end_date' => (int)$GamingSession->dbObject('EndDate')->format('U'), 
                                    'gamingsession_place_name' => $GamingSession->Venue()->exists() ? $GamingSession->Venue()->Location : $GamingSession->Location()->Location, 
                                    'gamingsession_name' => $GamingSession->Name, 
                                    'gamingsession_host' => $GamingSession->Member() ? $GamingSession->Member()->FirstName . ' ' . $GamingSession->Member()->Surname : '',
                                    'gamingsession_chat_id' => $chat ? $chat['id'] : '',
                                    'gamingsession_chat_name' => $chat ? $chat['name'] : '',
                                ], $GamingSession->Name, 'You have been invited to join a session.');
                            }
                        }
                    }
                }
            }
        }

        return (new JsonApi)->formatReturn(['gamingsession' => $GamingSession->getData(), 'invitations' => ['requested' => $requested, 'sent' => $sent]]);
    }

    /**
     * Gaming Session UID Handler
     *
     * @param SS_HTTPRequest $request
     * @return bool
     * @throws O_HTTPResponse_Exception
     */
    public function uidHandler(SS_HTTPRequest $request)
    {
        if (!($request->isGET() || $request->isPOST() || $request->isDELETE())) return $this->handleError(404, 'Must be a GET or POST or DELETE request');

        if($request->isGET()) {
            return $this->specificGamingSession($request);
        }

        if($request->isPOST()) {
            return $this->updateGamingSession($request);
        }

        if($request->isDELETE()) {
//            return $this->deleteGamingSession($request);
            return $this->cancelGamingSession($request);
        }
    }

    /**
     * Get Specific Gaming Session
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function specificGamingSession(SS_HTTPRequest $request)
    {
        if (! $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $GamingSession = $this->checkUuidExists($request->param('UID'), 'GamingSession')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        return (new JsonApi)->formatReturn(['gaming_session' => $GamingSession->getData()]);
    }

    /**
     * Update Gaming Session
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function updateGamingSession(SS_HTTPRequest $request)
    {
        if (! $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $GamingSession = $this->checkUuidExists($request->param('UID'), 'GamingSession')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        //  PERMISSION: Can only update gaming session if they are the host.
        if($GamingSession->MemberID != CurrentUser::getUserID()){
            return $this->handleError(404, 'TO DO: Whoops, you can not edit this gaming session!');
        }

        $data = $request->postVars();

        $privacyStatus = DB::get_schema()->enumValuesForField(new GamingSession(), 'PrivacySetting');
        
        if($this->checkMultipartContentType($request->getHeader('Content-Type'))) {
            
            if(isset($_FILES['image'])) {
 
                $image = $this->performUpload($_FILES['image'], $GamingSession, 'GamingSessions');

                if (! empty($image)) {
                    $GamingSession->GamingSessionImageID = $image->ID;
                }
            
            } 

        } else {
            return $this->handleError(2003);
        }

        if (isset($data['name']) && v::stringType()->validate($data['name'])) {
            $GamingSession->Name = $data['name'];
        }

        if (isset($data['description']) && v::stringType()->validate($data['description'])) {
            $GamingSession->Description = $data['description'];
        }

        if (isset($data['start_date']) && v::numeric()->validate($data['start_date'])) {
            $GamingSession->StartDate = date('Y-m-d H:i:s', $data['start_date']);
        }

        if (isset($data['end_date']) && v::numeric()->validate($data['end_date'])) {
            $GamingSession->EndDate = date('Y-m-d H:i:s', $data['end_date']);
        }

        // TO DO: Will be infinite if left blank.
        if (isset($data['player_limit']) && v::numeric()->validate($data['player_limit'])) {
            $GamingSession->PlayerLimit = $data['player_limit'];
        }

        if (isset($data['game_status']) && v::stringType()->validate($data['game_status'])) {
            $GamingSession->GameStatus = $data['game_status'];
        }

        if (isset($data['privacy_setting']) && v::stringType()->in($privacyStatus)->validate($data['privacy_setting'])) {
            $GamingSession->PrivacySetting = $data['privacy_setting'];
        }

        if (isset($data['hidden']) && v::boolType()->validate($data['hidden'])) {
            $GamingSession->Hidden = $data['hidden'];
        }
        
        if (isset($data['recurring']) && v::stringType()->in(DB::get_schema()->enumValuesForField(new GamingSession, 'Recurring'))->validate($data['recurring'])) {
            $GamingSession->Recurring = $data['recurring'];
        }

        if (isset($data['venue_uid']) && v::stringType()->validate($data['venue_uid'])) {

            if($Venue = $this->checkUuidExists($data['venue_uid'], 'Venue')) {
                $GamingSession->VenueID = $Venue->ID;
                $GamingSession->LocationID = 0;
            } else {
                return $this->handleError(404, 'TO DO: Venue UID wrong!');
            }
        } else {
            if (
                    isset($data['location']) && v::stringType()->validate($data['location']) &&
                    isset($data['location_latitude']) && v::numeric()->validate($data['location_latitude']) &&
                    isset($data['location_longitude']) && v::numeric()->validate($data['location_longitude'])
                    ) {
                
                $Location = Location::get()->filter(['Location' => $data['location'], 'LocationLatitude' => $data['location_latitude'], 'LocationLongitude' => $data['location_longitude']])->first();
                
                if(!$Location) {
                    
                    $Location = new Location;
                    $Location->Location = $data['location'];
                    $Location->LocationLatitude = $data['location_latitude'];
                    $Location->LocationLongitude = $data['location_longitude'];
                    
                    try {
                        $Location->write();
                    } catch (Exception $e) {
                        return $this->handleError(5000, $e->getMessage(), 400);
                    }
                }
                $GamingSession->LocationID = $Location->ID;
                $GamingSession->VenueID = 0;
                
            } else {
                
                return $this->handleError(404, 'TO DO: Pass Venue UID or valid Location parameters');
                
            }
        }

        if (isset($data['game_uid']) && v::stringType()->notEmpty()->validate($data['game_uid'])) {

            if($Game = $this->checkUuidExists($data['game_uid'], 'Game')) {
                $GamingSession->GameID = $Game->ID;
            } else {
                return $this->handleError(404, 'TO DO: Game UID wrong!');
            }
        }

        $GamingSession->MemberID = Member::get()->filter('ID', CurrentUser::getUserID())->first()->ID;

        try {
            $GamingSession->write();
            
            if (isset($Location)) {
                $Location->GamingSessions()->add($GamingSession->ID);
            }
            
            if(isset($Venue)) {
                $Venue->GamingSessions()->add($GamingSession->ID);
            }

            if(isset($Game)) {
                $Game->GamingSessions()->add($GamingSession->ID);
            }
        
            $requested = 0;
            $sent = 0;
            
            $attendees = $GamingSession->getAttendees();
            $invited = $GamingSession -> getInvited();
            
//            $chat = DB::prepared_query('SELECT * FROM "cometchat_chatrooms" WHERE "cometchat_chatrooms"."guid" = ?', array($GamingSession->ChatUUID))->first();
            $chat = null;
            if (isset($data['invitations']) && is_array($data['invitations'])) {
                
                $data['invitations'] = array_unique($data['invitations']);
                
                $requested = count($data['invitations']);

                foreach($invited as $singleInvited) {

                    if(!in_array($singleInvited->UUID, $data['invitations'])) {

                        foreach( GamingSessionInvitation::get()->filter(['GamingSessionID' => $GamingSession->ID, 'Status' => 'Pending', 'InvitationRecipientID' => $singleInvited->ID]) as $invitation ) {

                            $invitation->delete();

                        }

                        foreach( GamingSessionJoinRequest::get()->filter(['GamingSessionID' => $GamingSession->ID, 'Status' => 'Pending', 'RequestSenderID' => $singleInvited->ID]) as $invitation ) {

                            $invitation->delete();

                        }

                    }

                }
                
                foreach($attendees as $singleAttending) {
                    
                    $activeInvites = GamingSessionInvitation::get()->filter(['GamingSessionID' => $GamingSession->ID, 'Status' => 'Accepted', 'InvitationRecipientID' => $singleAttending->ID]);

                    foreach($activeInvites as $activeInvite) {
                        $activeInvite->Status = 'Cancelled';
                        $activeInvite->write();
                        // REMOVE USER FROM CHAT
//                        CometchatHelper::removeFromChatroom($GamingSession->ChatUUID, $singleAttending->ID);
                    }

                    $activeRequests = GamingSessionJoinRequest::get()->filter(['GamingSessionID' => $GamingSession->ID, 'Status' => 'Accepted', 'RequestSenderID' => $singleAttending->ID]);

                    foreach($activeRequests as $activeRequest) {
                        $activeRequest->Status = 'Rejected';
                        $activeRequest->write();
                        // REMOVE USER FROM CHAT
//                        CometchatHelper::removeFromChatroom($GamingSession->ChatUUID, $singleAttending->ID);
                    }
                }
                
                foreach($data['invitations'] as $key => $invitation )
                {
                    if($invited -> find('UUID', $invitation ) )
                    {
                        unset($data['invitations'][$key]);
                    }
                }
            
                foreach($data['invitations'] as $invitation) {

                    if($Member = $this->checkUuidExists($invitation, 'Member')) {

                        if($Member->ID != CurrentUser::getUserID()){

                            $Invitation = GamingSessionInvitation::create();
                            $Invitation->Status = 'Pending';
                            $Invitation->GamingSessionID = $GamingSession->ID;
                            $Invitation->InvitationSenderID = CurrentUser::getUserID();
                            $Invitation->InvitationRecipientID = $Member->ID;
                            if ($Invitation->write()) {
                                $sent++;
                                if(!empty($Member->PushToken)) {
                                    $push = new Fcm;
                                    $push->send($Member->PushToken, 'gamingsessioninvitation', [ 
                                        'gamingsession_uid' => $GamingSession->UUID, 
                                        'invitation_uid' => $Invitation->UUID,
                                        'gamingsession_start_date' => (int)$GamingSession->dbObject('StartDate')->format('U'), 
                                        'gamingsession_end_date' => (int)$GamingSession->dbObject('EndDate')->format('U'), 
                                        'gamingsession_place_name' => $GamingSession->Venue()->exists() ? $GamingSession->Venue()->Location : $GamingSession->Location()->Location, 
                                        'gamingsession_name' => $GamingSession->Name, 
                                        'gamingsession_host' => $GamingSession->Member() ? $GamingSession->Member()->FirstName . ' ' . $GamingSession->Member()->Surname : '',
                                        'gamingsession_chat_id' => $chat ? $chat['id'] : '',
                                        'gamingsession_chat_name' => $chat ? $chat['name'] : '',
                                    ], $GamingSession->Name, 'You have been invited to join a session.');
                                }
                            }
                        }
                    }
                }
            } else {
                // remove/cancell all invitations
                
                foreach(GamingSessionInvitation::get()->filter(['GamingSessionID' => $GamingSession->ID, 'Status' => 'Accepted']) as $acceptedInvitation) {
                    
                    $acceptedInvitation->Status = 'Cancelled';
                    $acceptedInvitation->write();
                    // REMOVE USER FROM CHAT
//                    CometchatHelper::removeFromChatroom($GamingSession->ChatUUID, $acceptedInvitation->InvitationRecipientID);
                    
                }
                
                foreach(GamingSessionInvitation::get()->filter(['GamingSessionID' => $GamingSession->ID, 'Status' => 'Pending']) as $pendingInvitation) {
                    
                    $pendingInvitation->delete();
                    
                }
                
                foreach(GamingSessionJoinRequest::get()->filter(['GamingSessionID' => $GamingSession->ID, 'Status' => 'Accepted']) as $acceptedRequest) {
                    
                    $acceptedRequest->Status = 'Cancelled';
                    $acceptedRequest->write();
                    // REMOVE USER FROM CHAT
//                    CometchatHelper::removeFromChatroom($GamingSession->ChatUUID, $acceptedRequest->RequestSenderID);
                    
                }
                
                foreach(GamingSessionJoinRequest::get()->filter(['GamingSessionID' => $GamingSession->ID, 'Status' => 'Pending']) as $pendingRequest) {
                    
                    $pendingRequest->delete();
                    
                }
                
            }
            
            foreach( $attendees as $attendee) {

                if(!empty($attendee->PushToken) && ($attendee->ID != $GamingSession->MemberID)) {
                    $push = new Fcm;
                    $push->send($attendee->PushToken, 'gamingsession', [ 
                        'gamingsession_uid' => $GamingSession->UUID,
                        'gamingsession_start_date' => (int)$GamingSession->dbObject('StartDate')->format('U'), 
                        'gamingsession_end_date' => (int)$GamingSession->dbObject('EndDate')->format('U'), 
                        'gamingsession_place_name' => $GamingSession->Venue()->exists() ? $GamingSession->Venue()->Location : $GamingSession->Location()->Location, 
                        'gamingsession_name' => $GamingSession->Name,
                        'gamingsession_host' => $GamingSession->Member() ? $GamingSession->Member()->FirstName . ' ' . $GamingSession->Member()->Surname : '',
                        'gamingsession_chat_id' => $chat ? $chat['id'] : '',
                        'gamingsession_chat_name' => $chat ? $chat['name'] : '',
                    ], $GamingSession->Name . ' has been updated.', 'Click to see changes.');
                }

            }

        } catch (Exception $e) {
            return $this->handleError(5000, $e->getMessage(), 400);
        }
        
        return (new JsonApi)->formatReturn(['gaming_session' => $GamingSession->getData(), 'invitations' => ['requested' => $requested, 'sent' => $sent]]);
    }

    /**
     * Gaming Session Image Handler
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function gamingSessionImage(SS_HTTPRequest $request)
    {
        if (!($request->isGET() || $request->isPOST())) return $this->handleError(404, 'Must be a GET or POST request');

        if($request->isGET()) {
            return $this->getGamingSessionImage($request);
        }

        if($request->isPOST()) {
            return $this->updateGamingSessionImage($request);
        }
    }

    /**
     * Get Gaming Session Image
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function getGamingSessionImage(SS_HTTPRequest $request)
    {
        if (! $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $GamingSession = $this->checkUuidExists($request->param('UID'), 'GamingSession')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        return (new JsonApi)->formatReturn(['image' => $GamingSession->getGamingSessionImage()]);
    }

    /**
     * Update Gaming Session Image
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function updateGamingSessionImage(SS_HTTPRequest $request)
    {
        if (! $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $GamingSession = $this->checkUuidExists($request->param('UID'), 'GamingSession')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        if($this->checkMultipartContentType($request->getHeader('Content-Type'))) {

            if(isset($_FILES['image'])) {
 
                $image = $this->performUpload($_FILES['image'], $GamingSession, 'GamingSessions');

                if (! empty($image)) {
                    $GamingSession->GamingSessionImageID = $image->ID;
                }
            
            } else {
                 return $this->handleError(404, 'TO DO: No gaming session image sent!');
            }
            
        } else {
            return $this->handleError(2003);
        }

        try {
            $GamingSession->write();
        } catch (Exception $e) {
            return $this->handleError(5000, $e->getMessage(), 400);
        }

        return (new JsonApi)->formatReturn([]);
    }

    /**
     * Gaming Session Result Endpoint Handler
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function gamingSessionResult(SS_HTTPRequest $request)
    {
        if($request->param('userUID')){

            if (!($request->isGET()  || $request->isPUT())) return $this->handleError(404, 'Must be a GET or PUT request');

            if($request->isGET()) {
                return $this->getSpecificGamingSessionResult($request);
            }

            if($request->isPUT()) {
                return $this->updateGamingSessionResult($request);
            }

        } else {

            if (! ($request->isGET() || $request->isPOST())) return $this->handleError(404, 'Must be a GET or POST request');

            if($request->isGET()) {
                return $this->getGamingSessionResult($request);
            }
            
            if($request->isPOST()) {
                return $this->createGamingSessionResult($request);
            }
        }
    }

    /**
     * Get Gaming Session Results
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function getGamingSessionResult(SS_HTTPRequest $request)
    {
        if (! $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $GamingSession = $this->checkUuidExists($request->param('UID'), 'GamingSession')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        $ResultsData = [
            'session_results' => []
        ];

        if($results = $GamingSession->GamingSessionResults()) {
            foreach ($results as $result) {
                if ($data = $result->getData()) {
                    $ResultsData['session_results'][] = $data;
                }
            }
        }

        return (new JsonApi)->formatReturn($ResultsData);
    }

    /**
     * Get Gaming Session Specific Result
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function getSpecificGamingSessionResult(SS_HTTPRequest $request)
    {
        if (! $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $GamingSession = $this->checkUuidExists($request->param('UID'), 'GamingSession')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        if (! $UserUID = $request->param('userUID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $Member = $this->checkUuidExists($UserUID, 'Member')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        $GamingSessionResult = GamingSessionResult::get()->filter(array('GamingSessionID' => $GamingSession->ID, 'MemberID' => $Member->ID))->first();

        return (new JsonApi)->formatReturn(['session_result' => $GamingSessionResult->getData()]);
    }

    /**
     * Create Gaming Session Result
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function createGamingSessionResult(SS_HTTPRequest $request)
    {
        if (! $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $GamingSession = $this->checkUuidExists($request->param('UID'), 'GamingSession')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }
        
        if(in_array($GamingSession->GameStatus, ['Completed', 'Cancelled'])) {
            return $this->handleError(404, 'TO DO: You can not create a gaming session result for completed or cancelled gaming session.');
        }

        //  PERMISSION: Can only update gaming session if they are the host.
        if($GamingSession->MemberID != CurrentUser::getUserID()){
            return $this->handleError(404, 'TO DO: Whoops, you can not create this gaming session result!');
        }

        $data = $this->getBody($request);
         
        $winners = !empty($data['winners']) ? $data['winners'] : [];    
        $commended = !empty($data['commended']) ? $data['commended'] : [];    

        $Result = DB::get_schema()->enumValuesForField(new GamingSessionResult(), 'Result');

        if (isset($data['result']) && v::stringType()->in($Result)->validate($data['result'])) {
            try {
                $GamingSession->Result = $data['result'];
                $GamingSession->GameStatus = 'Completed';
                $GamingSession->write();
                $GamingSessionResults = $GamingSession->GamingSessionResults();
                foreach($GamingSession->getAttendees() as $attendee) {
                    if(!$GamingSessionResults->filter(['MemberID' => $attendee->ID])->exists()){

                        $GamingSessionResult = GamingSessionResult::create();

                        if($data['result'] != 'Victory' || ($data['result'] == 'Victory' && in_array($attendee->UUID, $winners))){
                            $GamingSessionResult->Result = $data['result'];
                        } else {
                            $GamingSessionResult->Result = 'Defeat';
                        }

                        $GamingSessionResult->GamingSessionID = $GamingSession->ID;
                        $GamingSessionResult->MemberID = $attendee->ID;
                        $GamingSessionResult->write();
                        
                        if(!empty($attendee->PushToken)){
                            $push = new Fcm;
                            $push->send($attendee->PushToken, 'gamingsessionresult', [ 
                                'gamingsession_uid' => $GamingSession->UUID, 
                                'result_uid' => $GamingSessionResult->UUID,
                                'gamingsession_start_date' => (int)$GamingSession->dbObject('StartDate')->format('U'), 
                                'gamingsession_end_date' => (int)$GamingSession->dbObject('EndDate')->format('U'), 
                                'gamingsession_place_name' => $GamingSession->Venue()->exists() ? $GamingSession->Venue()->Location : $GamingSession->Location()->Location, 
                                'gamingsession_name' => $GamingSession->Name, 
                                'gamingsession_host' => $GamingSession->Member() ? $GamingSession->Member()->FirstName . ' ' . $GamingSession->Member()->Surname : '',
                            ],  $GamingSession->Member()->FirstName.' declared your result in gaming session '.$GamingSession->Name.' .');
                        }
                        
                        $dailyXp = $attendee->getDailyXP();
                        $calculatedXp = 0;
                        
                        if(in_array($attendee->UUID, $commended)) {
                            
                            $commend = Commend::create();
                            $commend->MemberID = $attendee->ID;
                            $commend->GamingSessionResultID = $GamingSessionResult->ID;
                            $commend->write();   
                            
                            $calculatedXp = $dailyXp + 10 > 200 ? 200 - $dailyXp : 10;
                            $xp = ExperiencePoint::create();
                            $xp->CommendID = $commend->ID;
                            $xp->Points = $calculatedXp;
                            $xp->write();
                            
                            $commend->ExperiencePointID = $xp->ID;
                            $commend->write();
                            
                            $GamingSessionResult->CommendID = $commend->ID;
                        }
                        
                        $dailyXp += $calculatedXp;
                        
                        $xp = ExperiencePoint::create();
                        $xp->GamingSessionResultID = $GamingSessionResult->ID;

                        if($GamingSessionResult->Result == 'Victory'){
                            $calculatedXp = $dailyXp + 50 > 200 ? 200 - $dailyXp : 50;
                            $xp->Points = $calculatedXp; // max 50xp
                        } else if($GamingSessionResult->Result == 'Defeat') {
                            $calculatedXp = $dailyXp + 10 > 200 ? 200 - $dailyXp : 10;
                            $xp->Points = $calculatedXp; // max 10xp
                        } else if($GamingSessionResult->Result == 'Stalemate'){
                            $calculatedXp = $dailyXp + 25 > 200 ? 200 - $dailyXp : 25;
                            $xp->Points = $calculatedXp; // max 25xp
                        } else {
                            $xp->Points = 0; // 0xp
                        }

                        $xp->write();

                        $GamingSessionResult->ExperiencePointID = $xp->ID;
                        $GamingSessionResult->write();

                        $GamingSession->GamingSessionResults()->add($GamingSessionResult);
                        $attendee->GamingSessionResults()->add($GamingSessionResult);
                    }
                }
                if($GamingSession->Recurring != "''") {
                    $this->_createRecurringSession($GamingSession);
                }
            } catch (Exception $e) {

                return $this->handleError(5000, $e->getMessage(), 400);
            }
        } else {
            
            return $this->handleError(404, 'TO DO: Invalid result');
        }
        
        return (new JsonApi)->formatReturn(['result' => $GamingSession->Result]);
        


    }

    /**
     * Update Gaming Session Result
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function updateGamingSessionResult(SS_HTTPRequest $request)
    {
        if (! $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $GamingSession = $this->checkUuidExists($request->param('UID'), 'GamingSession')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        if (! $UserUID = $request->param('userUID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $Member = $this->checkUuidExists($UserUID, 'Member')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        //  PERMISSION: Can only update gaming session if they are the host.
        if($GamingSession->MemberID != CurrentUser::getUserID()){
            return $this->handleError(404, 'TO DO: Whoops, you can not edit this gaming session result!');
        }

        // Check Member is an attendee of this gaming session
        if(! $GamingSession->checkGamingSessionPlayer($GamingSession->getAttendees(), $Member)){
            return $this->handleError(404, 'TO DO: This player is not an attendee of this gaming session!');
        }

        $data = $this->getBody($request);

        $GamingSessionResult = GamingSessionResult::get()->filter(array('GamingSessionID' => $GamingSession->ID, 'MemberID' => $Member->ID))->first();

        $xp = ExperiencePoint::get()->filter('GamingSessionResultID', $GamingSessionResult->ID)->first();

        $Result = DB::get_schema()->enumValuesForField(new GamingSessionResult(), 'Result');

        if (isset($data['result']) && v::stringType()->in($Result)->validate($data['result'])) {

            $GamingSessionResult->Result = $data['result'];

            if($data['result'] == 'Victory'){
                $xp->Points = 50; // 50xp
            } else if($data['result'] == 'Defeat') {
                $xp->Points = 10; // 10xp
            } else if($data['result'] == 'Stalemate'){
                $xp->Points = 25; // 25xp
            } else {
                $xp->Points = 0; // 0xp
            }
        }

        try {

            $GamingSessionResult->write();

            $xp->write();

            return (new JsonApi)->formatReturn(['result' => $GamingSessionResult->Result]);

        } catch (Exception $e) {

            return $this->handleError(5000, $e->getMessage(), 400);
        }
    }

    /**
     * Gaming Session Invitations Endpoints handler
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse|void
     * @throws O_HTTPResponse_Exception
     */
    public function gamingSessionInvitations(SS_HTTPRequest $request)
    {
        // Status could be accept or reject invitation.
        if($request->param('invitationUID') && $request->param('status')) {

            if (!$request->isPUT()) return $this->handleError(404, 'Must be a PUT request');

            if($request->isPUT()) {
                return $this->setGamingSessionInvitationStatus($request);
            }
        // Get specific gaming session invitation
        } else if ($request->param('invitationUID') && ! $request->param('status')) {
            if (!$request->isGet()) return $this->handleError(404, 'Must be a GET request');

            if($request->isGET()) {
                return $this->getSpecificGamingSessionInvitation($request);
            }
        } else {

            if (!($request->isGet() || $request->isPOST())) return $this->handleError(404, 'Must be a GET or POST request');

            if($request->isGET()) {
                return $this->getAllGamingSessionInvitations($request);
            }
            
            if ($request->isPOST()) {
                return $this->createGamingSessionInvitation($request);
            }
        }
    }

    /**
     * Get All Gaming Session Invitations
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function getAllGamingSessionInvitations(SS_HTTPRequest $request)
    {
        if (! $UID = $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $GamingSession = $this->checkUuidExists($UID, 'GamingSession')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }
        
        $GamingSessionInvitations = [ 'session_invitations' => []];

        if($Invitations = GamingSessionInvitation::get()->filter('GamingSessionID', $GamingSession->ID)->sort('Created ASC')) {

            foreach($Invitations as $invitation) {

                if($data = $invitation->getData()) {

                    $GamingSessionInvitations['session_invitations'][] = $data;
                }
            }
        }

        return (new JsonApi)->formatReturn($GamingSessionInvitations);
    }

    /**
     * Get Specific Gaming Session Invitation
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function getSpecificGamingSessionInvitation(SS_HTTPRequest $request)
    {
        if (! $UID = $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $GamingSession = $this->checkUuidExists($UID, 'GamingSession')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        if (! $InvitationUID = $request->param('invitationUID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $Invitation = $this->checkUuidExists($InvitationUID, 'GamingSessionInvitation')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        return (new JsonApi)->formatReturn(['session_invitation' => $Invitation->getData()]);
    }

    /**
     * Create Gaming Session Invitation
     * Permissions: Can only send invitations if player is the host of the gaming session.
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function createGamingSessionInvitation(SS_HTTPRequest $request)
    {
        // TO DO: Check pending join requests to see if player has asked to join gaming session already.

        if (! $UID = $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $GamingSession = $this->checkUuidExists($UID, 'GamingSession')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        if(in_array($GamingSession->GameStatus, ['Completed', 'Cancelled'])) {
            return $this->handleError(404, 'TO DO: You can not create a gaming session invitation for completed or cancelled gaming session.');
        }

        // Check current user is the host of the gaming session.
        if($GamingSession->MemberID != CurrentUser::getUserID()){
            return $this->handleError(404, 'TO DO: You do not host this gaming session!');
        }

        $data = $this->getBody($request);
        
        $requested = 0;
        $sent = 0;
        
//        $chat = DB::prepared_query('SELECT * FROM "cometchat_chatrooms" WHERE "cometchat_chatrooms"."guid" = ?', array($GamingSession->ChatUUID))->first();
        $chat = null;
        if(( $GamingSession->PlayerLimit == -1 ) || ( $GamingSession->CurrentPlayerNumber < $GamingSession->PlayerLimit )) {
            
            if(isset($data['users']) && is_array($data['users'])) {

                $requested = count($data['users']);

                $data['users'] = array_unique($data['users']);
                
                foreach($data['users'] as $UserUID) {

                        if($Member = $this->checkUuidExists($UserUID, 'Member')) {

                            if($Member->ID != CurrentUser::getUserID() ){

                                $JoinRequests = GamingSessionJoinRequest::get()->filter(array('GamingSessionID' => $GamingSession->ID, 'RequestSenderID' => $Member->ID, 'RequestRecipientID' => CurrentUser::getUserID()))->exclude('Status', ['Cancelled', 'Rejected']);
                                if($JoinRequests->exists()) {
                                    continue;
                                }

                                $Invitations = GamingSessionInvitation::get()->filter(array('GamingSessionID' => $GamingSession->ID, 'InvitationSenderID' => CurrentUser::getUserID(), 'InvitationRecipientID' => $Member->ID))->exclude('Status', ['Cancelled', 'Rejected']);
                                if($Invitations->exists()) {
                                    continue;
                                }

                                $Invitation = GamingSessionInvitation::create();
                                $Invitation->Status = 'Pending';
                                $Invitation->GamingSessionID = $GamingSession->ID;
                                $Invitation->InvitationSenderID = CurrentUser::getUserID();
                                $Invitation->InvitationRecipientID = $Member->ID;

                                if($Invitation->write()) {
                                    $sent++;
                                    if(!empty($Member->PushToken)) {
                                        $push = new Fcm;
                                        $push->send($Member->PushToken, 'gamingsessioninvitation', [
                                            'gamingsession_uid' => $GamingSession->UUID, 
                                            'invitation_uid' => $Invitation->UUID,
                                            'gamingsession_start_date' => (int)$GamingSession->dbObject('StartDate')->format('U'), 
                                            'gamingsession_end_date' => (int)$GamingSession->dbObject('EndDate')->format('U'), 
                                            'gamingsession_place_name' => $GamingSession->Venue()->exists() ? $GamingSession->Venue()->Location : $GamingSession->Location()->Location,
                                            'gamingsession_name' => $GamingSession->Name,  
                                            'gamingsession_host' => $GamingSession->Member() ? $GamingSession->Member()->FirstName . ' ' . $GamingSession->Member()->Surname : '',
                                            'gamingsession_chat_id' => $chat ? $chat['id'] : '',
                                            'gamingsession_chat_name' => $chat ? $chat['name'] : '',
                                        ], $GamingSession->Name, 'You have been invited to join a session.');
                                    }

                                }                            

                            }

                        }

                    }

            }

        } 

        return (new JsonApi)->formatReturn(['requested' => $requested, 'sent' => $sent]);
    }

    /**
     * Accept / Reject Gaming Session Invitation
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function setGamingSessionInvitationStatus(SS_HTTPRequest $request)
    {
        // TO DO: Should users be allowed to edit their invitation a second time?? Prevent: don't update if status != Pending.
        if (! $request->isPUT()) return $this->handleError(404, 'Must be a PUT request');

        if (! $UID = $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $GamingSession = $this->checkUuidExists($UID, 'GamingSession')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        if (! $InvitationUID = $request->param('invitationUID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $Invitation = $this->checkUuidExists($InvitationUID, 'GamingSessionInvitation')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }
        
        if($Invitation->InvitationRecipient()->LocalGamingStoreAccount) {
            return $this->handleError(404, 'As a Local Gaming Store you cannot respond to an invitation');
        }

//        $Invitation = $GamingSession->GamingSessionInvitations()->filter(['Status' => 'Pending', 'InvitationRecipientID' => CurrentUser::getUserID()])->first();
//        
//        if(! $Invitation = $this->checkUuidExists($Invitation, 'GamingSessionInvitation')) {
//            return $this->handleError(404, 'No pending invitation to this gaming session.');
//        }
        
        
        if (! $status = $request->param('status')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }
        

        if($status == 'accept'){
            
            if($GamingSession->CurrentPlayerNumber == $GamingSession->PlayerLimit) {
                
                return $this->handleError(404, 'TO DO: Session reached its players limit!');
                
            }

            $Invitation->Status = 'Accepted';
            
            //ADD USER TO CHAT
//            CometchatHelper::addToChatroom($GamingSession->ChatUUID, $Invitation->InvitationRecipientID);
//            CometchatHelper::sendInviteMessage($Invitation->InvitationSenderID, $GamingSession->Name);
        } else if ($status == 'reject'){

            $Invitation->Status = 'Rejected';
            
            //REMOVE USER FROM CHAT - even if not ever added to chat, call will just fail and return 0
//            CometchatHelper::removeFromChatroom($GamingSession->ChatUUID, $Invitation->InvitationRecipientID);
            
        } else {

            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }
        
        try {
        
            $Invitation->write();
//            $chat = DB::prepared_query('SELECT * FROM "cometchat_chatrooms" WHERE "cometchat_chatrooms"."guid" = ?', array($GamingSession->ChatUUID))->first();
            $chat = null;
            if(!empty($Invitation->InvitationSender()->PushToken)) {
                $push = new Fcm;
                $push->send($Invitation->InvitationSender()->PushToken, 'gamingsessioninvitation_response', [ 
                    'gamingsession_uid' => $GamingSession->UUID, 
                    'invitation_uid' => $Invitation->UUID,
                    'gamingsession_start_date' => (int)$GamingSession->dbObject('StartDate')->format('U'), 
                    'gamingsession_end_date' => (int)$GamingSession->dbObject('EndDate')->format('U'), 
                    'gamingsession_place_name' => $GamingSession->Venue()->exists() ? $GamingSession->Venue()->Location : $GamingSession->Location()->Location,
                    'gamingsession_name' => $GamingSession->Name, 
                    'gamingsession_host' => $GamingSession->Member() ? $GamingSession->Member()->FirstName . ' ' . $GamingSession->Member()->Surname : '',
                    'gamingsession_chat_id' => $chat ? $chat['id'] : '',
                    'gamingsession_chat_name' => $chat ? $chat['name'] : '',
                ], $Invitation->InvitationRecipient()->FirstName . ' just '. $status . 'ed your invitation to ' . $Invitation->GamingSession()->Name);
            }
            
            if(($Invitation->Status === 'Accepted') && ($GamingSession->PrivacySetting === 'Public')) {
                $place = $GamingSession->Venue() -> ID ? $GamingSession->Venue() : $GamingSession->Location();
                foreach($Invitation->InvitationRecipient()->getFriends() as $friend) { 
                    if((abs($place->LocationLongitude - $friend['location_longitude']) < 0.1) && (abs($place->LocationLatitude - $friend['location_latitude']) < 0.1)) {
                        $Member = Member::get()->byID($friend['member_id']);
                        if(!empty($Member->PushToken) && $GamingSession->getAttendees()->byID($Member->ID)) {
                            $push = new Fcm;
                            $push->send($Member->PushToken, 'gamingsession_friendnearby', [ 
                                'gamingsession_uid' => $GamingSession->UUID,
                            ], 'Your friend joined nearby session!', $Invitation->InvitationRecipient()->FirstName . ' ' . $Invitation->InvitationRecipient()->Surname. ' has joined to session' . $GamingSession->Name);
                        }
                    }
                }
            }
            
            return (new JsonApi)->formatReturn([]);

        } catch (Exception $e) {

            return $this->handleError(5000, $e->getMessage(), 400);
        }
    }

    /**
     * Gaming Session Join Requests Endpoints handler
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse|void
     * @throws O_HTTPResponse_Exception
     */
    public function gamingSessionRequests(SS_HTTPRequest $request)
    {
        // Status could be accept or reject join request.
        if($request->param('joinrequestUID') && $request->param('status')) {

            if (!$request->isPUT()) return $this->handleError(404, 'Must be a PUT request');

            if($request->isPUT()) {
                return $this->setGamingSessionJoinRequestStatus($request);
            }
        } else if ($request->param('joinrequestUID') && ! $request->param('status')) {

            if (! $request->isGet()) return $this->handleError(404, 'Must be a GET request');

            if($request->isGET()) {
                return $this->getSpecificGamingSessionJoinRequest($request);
            }

        } else {
            
            if (! ($request->isGET() || $request->isPOST())) return $this->handleError(404, 'Must be a GET or POST request');

            if ($request->isPOST()) {
                return $this->createGamingSessionJoinRequest($request);
            }
            
            if($request->isGET()) {
                return $this->getAllGamingSessionJoinRequests($request);
            }
        }
    }

    /**
     * Get All Gaming Session Join Requests
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function getAllGamingSessionJoinRequests(SS_HTTPRequest $request)
    {
        if (! $UID = $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $GamingSession = $this->checkUuidExists($UID, 'GamingSession')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        $GamingSessionJoinRequests = [ 'join_requests' => []];

        if($JoinRequests = GamingSessionJoinRequest::get()->filter('GamingSessionID', $GamingSession->ID)->sort('Created ASC')) {

            foreach($JoinRequests as $joinRequest) {

                if($data = $joinRequest->getData()) {

                    $GamingSessionJoinRequests['join_requests'][] = $data;
                }
            }
        }

        return (new JsonApi)->formatReturn($GamingSessionJoinRequests);
    }

    /**
     * Get Specific Gaming Session Join Request
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function getSpecificGamingSessionJoinRequest(SS_HTTPRequest $request)
    {
        if (! $UID = $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $GamingSession = $this->checkUuidExists($UID, 'GamingSession')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        if (! $JoinRequestUID = $request->param('joinrequestUID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $JoinRequest = $this->checkUuidExists($JoinRequestUID, 'GamingSessionJoinRequest')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        return (new JsonApi)->formatReturn(['join_request' => $JoinRequest->getData()]);
    }

    /**
     * Create Gaming Session Join Request
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function createGamingSessionJoinRequest(SS_HTTPRequest $request)
    {
        
        if(CurrentUser::getUser()->LocalGamingStoreAccount) {
            return $this->handleError(404, 'As a Local Gaming Store you cannot create join requests');
        }
        
        if (! $UID = $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $GamingSession = $this->checkUuidExists($UID, 'GamingSession')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        if(in_array($GamingSession->GameStatus, ['Completed', 'Cancelled'])) {
            return $this->handleError(404, 'TO DO: Yo can not join a completed or cancelled gaming session.');
        }
        
        // Check not sending invitation to current user.
        if($GamingSession->MemberID == CurrentUser::getUserID()){
            return $this->handleError(404, 'TO DO: You can not send a join request to yourself - duh!');
        }
        
//        if(GamingSessionJoinRequest::get() -> filter(['GamingSessionID' =>  $GamingSession -> ID, 'RequestSenderID' => CurrentUser::getUserID()]) -> count() > 0)
//        {
//            return $this->handleError(404, 'You have already requested for this session !');
//        }
//        $chat = DB::prepared_query('SELECT * FROM "cometchat_chatrooms" WHERE "cometchat_chatrooms"."guid" = ?', array($GamingSession->ChatUUID))->first();
        $chat = null;
        // Get all non-rejected invitations sent for this gaming session by host and prevent duplication invitations.
        $Invitations = GamingSessionInvitation::get()->filter(array('GamingSessionID' => $GamingSession->ID, 'InvitationSenderID' => CurrentUser::getUserID()))->exclude('Status', ['Rejected', 'Cancelled']);
        foreach($Invitations as $invitation){

            if($invitation->InvitationRecipientID == CurrentUser::getUserID() && $invitation->Status == 'Accepted'){
                return $this->handleError(404, 'TO DO: You are already a participant of this gaming session.');
            }
            if($invitation->InvitationRecipientID == CurrentUser::getUserID() && $invitation->Status == 'Pending'){
                return $this->handleError(404, 'TO DO: You have already been sent an invite to this gaming session, please respond.');
            }
        }

        // Get all non-rejected join requests sent for this gaming session and prevent duplication invitations.
        $JoinRequests = GamingSessionJoinRequest::get()->filter(array('GamingSessionID' => $GamingSession->ID, 'RequestSenderID' => CurrentUser::getUserID()))->exclude('Status', ['Rejected']);
        foreach($JoinRequests as $joinRequest){

            if($joinRequest->RequestSenderID == CurrentUser::getUserID() && $joinRequest->Status == 'Accepted'){
                return $this->handleError(404, 'TO DO: You are already participant of this gaming session.');
            }
            if($joinRequest->RequestSenderID == CurrentUser::getUserID() && $joinRequest->Status == 'Pending'){
                return $this->handleError(404, 'TO DO: You have already requested to join this gaming session.');
            }
            if($joinRequest->RequestSenderID == CurrentUser::getUserID() && $joinRequest->Status == 'Cancelled'){
                $JoinRequest = $joinRequest;
            }
        }

        if(( $GamingSession->PlayerLimit == -1 ) || ( $GamingSession->CurrentPlayerNumber < $GamingSession->PlayerLimit )) {
            
            if(!isset($joinRequest))
            {
                $JoinRequest = GamingSessionJoinRequest::create();
            }

            // Check gaming session is public, if it is make user a gaming session member, else send join request.
            // TO DO: Send push notification to say a player has joined your session
            if($GamingSession->PrivacySetting == 'Public'){
                $JoinRequest->Status = 'Accepted';
                $join = 'joined';
                $type = 'join_info';
            } else {
                $JoinRequest->Status = 'Pending';
                $join = 'wants to join';
                $type = 'joinrequest';
            }

            $JoinRequest->GamingSessionID = $GamingSession->ID;
            $JoinRequest->RequestSenderID = CurrentUser::getUserID();
            $JoinRequest->RequestRecipientID = $GamingSession->MemberID;
            $JoinRequest->write();

            if($GamingSession->Member() && !empty($GamingSession->Member()->PushToken)) {
                $push = new Fcm;
                $push->send($GamingSession->Member()->PushToken, $type, [ 
                    'gamingsession_uid' => $GamingSession->UUID, 
                    'joinrequest_uid' => $JoinRequest->UUID,
                    'gamingsession_start_date' => (int)$GamingSession->dbObject('StartDate')->format('U'), 
                    'gamingsession_end_date' => (int)$GamingSession->dbObject('EndDate')->format('U'), 
                    'gamingsession_place_name' => $GamingSession->Venue()->exists() ? $GamingSession->Venue()->Location : $GamingSession->Location()->Location,
                    'gamingsession_name' => $GamingSession->Name, 
                    'gamingsession_host' => $GamingSession->Member() ? $GamingSession->Member()->FirstName . ' ' . $GamingSession->Member()->Surname : '',
                    'gamingsession_chat_id' => $chat ? $chat['id'] : '',
                    'joinrequest_user_id' => CurrentUser::getUserID(),
                    'gamingsession_chat_name' => $chat ? $chat['name'] : '',
                ], $GamingSession->Name, CurrentUser::getUser()->FirstName . ' ' . $join . ' your session ' . $GamingSession->Name );
            }
            
            if(($JoinRequest->Status === 'Accepted') && ($GamingSession->PrivacySetting === 'Public')) {
                $place = $GamingSession->Venue() -> ID ? $GamingSession->Venue() : $GamingSession->Location();
                foreach($JoinRequest->RequestSender()->getFriends() as $friend) { 
                    if((abs($place->LocationLongitude - $friend['location_longitude']) < 0.1) && (abs($place->LocationLatitude - $friend['location_latitude']) < 0.1)) {
                        $Member = Member::get()->byID($friend['member_id']);
                        if(!empty($Member->PushToken) && !$GamingSession->getAttendees()->byID($Member->ID)) {
                            $push = new Fcm;
                            $push->send($Member->PushToken, 'gamingsession_friendnearby', [ 
                                'gamingsession_uid' => $GamingSession->UUID,
                            ], 'Your friend joined nearby session!', $JoinRequest->RequestSender()->FirstName . ' ' . $JoinRequest->RequestSender()->Surname. ' has joined to session' . $GamingSession->Name);
                        }
                    }
                }
            }

            return (new JsonApi)->formatReturn(['session_join_request' => ['uid' => $JoinRequest->UUID, 'join_request_status' => $JoinRequest->Status]]);
            
        } else {
            
            return $this->handleError(404, 'TO DO: Session is full.');
            
        }
    }

    /**
     * Accept / Reject Gaming Session
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function setGamingSessionJoinRequestStatus(SS_HTTPRequest $request)
    {
        // TO DO: Should users be allowed to edit their join request a second time?? Prevent: don't update if status != Pending.
        if (! $request->isPUT()) return $this->handleError(404, 'Must be a PUT request');

        if (! $UID = $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $GamingSession = $this->checkUuidExists($UID, 'GamingSession')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        if (! $JoinRequestUID = $request->param('joinrequestUID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $JoinRequest = $this->checkUuidExists($JoinRequestUID, 'GamingSessionJoinRequest')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        if (! $status = $request->param('status')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if($status == 'accept'){

            if($GamingSession->CurrentPlayerNumber == $GamingSession->PlayerLimit) {
                
                return $this->handleError(404, 'TO DO: Session reached its players limit!');
                
            }
            
            $JoinRequest->Status = 'Accepted';
            
            //ADD USER TO CHAT
//            CometchatHelper::addToChatroom($GamingSession->ChatUUID, $JoinRequest->RequestSenderID);
//            CometchatHelper::sendInviteMessage($JoinRequest->RequestSenderID, $GamingSession->Name);

        } else if ($status == 'reject'){

            $JoinRequest->Status = 'Rejected';
            
            //REMOVE USER FROM CHAT
//            CometchatHelper::removeFromChatroom($GamingSession->ChatUUID, $JoinRequest->RequestSenderID);

        } else {

            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

//        $chat = DB::prepared_query('SELECT * FROM "cometchat_chatrooms" WHERE "cometchat_chatrooms"."guid" = ?', array($GamingSession->ChatUUID))->first();
        $chat = null;
        try {
        
            $JoinRequest->write();
            
            if(!empty($JoinRequest->RequestSender()->PushToken)) {
                $push = new Fcm;
                $push->send($JoinRequest->RequestSender()->PushToken, 'joinrequest_response', [ 
                    'gamingsession_uid' => $GamingSession->UUID, 
                    'joinrequest_uid' => $JoinRequest->UUID,
                    'gamingsession_start_date' => (int)$GamingSession->dbObject('StartDate')->format('U'), 
                    'gamingsession_end_date' => (int)$GamingSession->dbObject('EndDate')->format('U'), 
                    'gamingsession_place_name' => $GamingSession->Venue()->exists() ? $GamingSession->Venue()->Location : $GamingSession->Location()->Location,
                    'gamingsession_name' => $GamingSession->Name, 
                    'gamingsession_host' => $GamingSession->Member() ? $GamingSession->Member()->FirstName . ' ' . $GamingSession->Member()->Surname : '',                    
                    'gamingsession_chat_id' => $chat ? $chat['id'] : '',
                    'gamingsession_chat_name' => $chat ? $chat['name'] : '',
                ], $JoinRequest->RequestRecipient()->FirstName . ' just '. $status . 'ed your join request to ' . $JoinRequest->GamingSession()->Name);
            }
            
            return (new JsonApi)->formatReturn([]);

        } catch (Exception $e) {

            return $this->handleError(5000, $e->getMessage(), 400);
        }
    }
    
    /**
     * Delete Gaming Session
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function deleteGamingSession(SS_HTTPRequest $request) {
        if (! $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $GamingSession = $this->checkUuidExists($request->param('UID'), 'GamingSession')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        //  PERMISSION: Can only delete gaming session if they are the host.
        if($GamingSession->MemberID != CurrentUser::getUserID()){
            return $this->handleError(404, 'TO DO: Whoops, you can not edit this gaming session!');
        }

        try {
            
            $Member = $GamingSession->Member();
            
            if(isset($Member)) {
                $Member->GamingSessions()->remove($GamingSession);
            }
            
            $Venue = $GamingSession->Venue();
            
            if(isset($Venue)) {
                $Venue->GamingSessions()->remove($GamingSession);
            }

            $Game = $GamingSession->Game();
            
            if(isset($Game)) {
                $Game->GamingSessions()->remove($GamingSession);
            }
            
            $GamingSessionInvitations = GamingSessionInvitation::get()->filter(['GamingSessionID' => $GamingSession->ID]);
            
            foreach($GamingSessionInvitations as $GamingSessionInvitation) {
                $GamingSessionInvitation->delete();
            }
            
            $GamingSessionJoinRequests = GamingSessionJoinRequest::get()->filter(['GamingSessionID' => $GamingSession->ID]);
            
            foreach($GamingSessionJoinRequests as $GamingSessionJoinRequest) {
                $GamingSessionJoinRequest->delete();
            }
            
            $GamingSessionResults = GamingSessionResult::get()->filter(['GamingSessionID' => $GamingSession->ID]);
            
            foreach($GamingSessionResults as $GamingSessionResult) {
                $GamingSessionResult->delete();
            }

            $GamingSession->delete();
            
        } catch (Exception $e) {
            return $this->handleError(5000, $e->getMessage(), 400);
        }

        return (new JsonApi)->formatReturn(['status' => 'success']);
    }
    
    /**
     * Cancell Gaming Session
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function cancelGamingSession(SS_HTTPRequest $request) {
        if (! $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $GamingSession = $this->checkUuidExists($request->param('UID'), 'GamingSession')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        //  PERMISSION: Can only cancel gaming session if they are the host.
        if($GamingSession->MemberID != CurrentUser::getUserID()){
            return $this->handleError(404, 'TO DO: Whoops, you can not edit this gaming session!');
        }

//        $chat = DB::prepared_query('SELECT * FROM "cometchat_chatrooms" WHERE "cometchat_chatrooms"."guid" = ?', array($GamingSession->ChatUUID))->first();
        $chat = null;
        try {
            
            $GamingSession->GameStatus = 'Cancelled';
            $GamingSession->Result = 'No Session';
            $GamingSession->write();
            
        } catch (Exception $e) {
            return $this->handleError(5000, $e->getMessage(), 400);
        }
        
        foreach($GamingSession->getAttendees() as $attendee) {
            
            if(!empty($attendee->PushToken) && ($attendee->ID != $GamingSession->MemberID)) {
                $push = new Fcm;
                $push->send($attendee->PushToken, 'gamingsession', [ 
                    'gamingsession_uid' => $GamingSession->UUID,
                    'gamingsession_start_date' => (int)$GamingSession->dbObject('StartDate')->format('U'), 
                    'gamingsession_end_date' => (int)$GamingSession->dbObject('EndDate')->format('U'), 
                    'gamingsession_place_name' => $GamingSession->Venue()->exists() ? $GamingSession->Venue()->Location : $GamingSession->Location()->Location,
                    'gamingsession_name' => $GamingSession->Name, 
                    'gamingsession_host' => $GamingSession->Member() ? $GamingSession->Member()->FirstName . ' ' . $GamingSession->Member()->Surname : '',
                    'gamingsession_chat_id' => $chat ? $chat['id'] : '',
                    'gamingsession_chat_name' => $chat ? $chat['name'] : '',
                ], $GamingSession->Name . ' has been cancelled.');
            }
            
        }
        
        if($GamingSession->Recurring != "''") {
            $this->_createRecurringSession($GamingSession);
        }

        return (new JsonApi)->formatReturn(['status' => 'success']);
    }
    
    public function gamingSessionAttendance(SS_HTTPRequest $request) {
        
        
        if (! $UID = $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $GamingSession = $this->checkUuidExists($UID, 'GamingSession')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }
        
        if (! $request->isDelete()) return $this->handleError(404, 'Must be a DELETE request');
        
        if($request->isDelete()) {
            return $this->cancelGamingSessionAttendance($request, $GamingSession);
        }   
    }
    
    /**
     * Create Gaming Session Rematch
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function gamingSessionRematch(SS_HTTPRequest $request)
    {
        if (! $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $GamingSessionOld = $this->checkUuidExists($request->param('UID'), 'GamingSession')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }
        
        if(!$GamingSessionOld->checkGamingSessionPlayer($GamingSessionOld->getAttendees(), CurrentUser::getUser())) {
            return $this->handleError(404, 'TO DO: Whoops, you can request a rematch of this gaming session!');
        }
        
        $CreatorId = CurrentUser::getUserID();
        
        $GamingSession = GamingSession::create();

        $GamingSession->Name = $GamingSessionOld->Name;

        $GamingSession->Description = $GamingSessionOld->Description;

        $GamingSession->StartDate = date('Y-m-d H:i:s');

        $GamingSession->EndDate = date('Y-m-d H:i:s');

        $GamingSession->PlayerLimit = $GamingSessionOld->PlayerLimit;

        $GamingSession->GameStatus = $GamingSessionOld->GameStatus;

        $GamingSession->Location = $GamingSessionOld->Location;

        $GamingSession->LocationLatitude = $GamingSessionOld->LocationLatitude;

        $GamingSession->LocationLongitude = $GamingSessionOld->LocationLongitude;

        $GamingSession->PrivacySetting = $GamingSessionOld->PrivacySetting;

        $GamingSession->Hidden = $GamingSessionOld->Hidden;

        $GamingSession->VenueID = $GamingSessionOld->VenueID;

        $GamingSession->GameID = $GamingSessionOld->GameID;

        $GamingSession->MemberID = $CreatorId;

        try {
            $GamingSession->write();
            
            foreach($GamingSessionOld->getAttendees() as $attendee) {
                
                if($CreatorId != $attendee->ID) {
                    $Invitation = GamingSessionInvitation::create();
                    $Invitation->Status = 'Pending';
                    $Invitation->GamingSessionID = $GamingSession->ID;
                    $Invitation->InvitationSenderID = $CreatorId;
                    $Invitation->InvitationRecipientID = $attendee->ID;
                    $Invitation->write();
                }
            }
            
        } catch (Exception $e) {
            return $this->handleError(5000, $e->getMessage(), 400);
        }

        return (new JsonApi)->formatReturn(['gamingsession' => $GamingSession->getData()]);
    }
    
    private function _createRecurringSession($SourceGamingSession) {

        if($SourceGamingSession->Recurring == 'Weekly') {
            $timeString = ' +1 week';
        } elseif($SourceGamingSession->Recurring == 'Fortnightly') {
            $timeString = ' +2 weeks';
        } elseif($SourceGamingSession->Recurring == 'Monthly') {
            $timeString = ' +1 month';
        } else {
            $timeString = ' +1 month';
        }
        $GamingSession = GamingSession::create();
        
        $GamingSession->Name = $SourceGamingSession->Name;

        $GamingSession->Description = $SourceGamingSession->Description;

        $GamingSession->StartDate = date('Y-m-d H:i:s', strtotime($SourceGamingSession->StartDate.$timeString));
        
        $GamingSession->EndDate = date('Y-m-d H:i:s', strtotime($SourceGamingSession->EndDate.$timeString));

        $GamingSession->PlayerLimit = $SourceGamingSession->PlayerLimit;

        $GamingSession->GameStatus = 'Active';
        
        $GamingSession->PrivacySetting = $SourceGamingSession->PrivacySetting;
        
        $GamingSession->Hidden = $SourceGamingSession->Hidden;
        
        $GamingSession->Recurring = $SourceGamingSession->Recurring;
        
        $GamingSession->VenueID = $SourceGamingSession->VenueID;
        
        $GamingSession->LocationID = $SourceGamingSession->LocationID;
        
        $GamingSession->GameID = $SourceGamingSession->GameID;
        
        $GamingSession->MemberID = $SourceGamingSession->MemberID;
        
        $GamingSession->GamingSessionImageID = $SourceGamingSession->GamingSessionImageID;

        try {
            
            // CREATE CHAT
//            $GamingSession->ChatUUID = CometchatHelper::addChatroom($GamingSession->MemberID, $GamingSession->Name .' '. $GamingSession->StartDate);
//            $chat = DB::prepared_query('SELECT * FROM "cometchat_chatrooms" WHERE "cometchat_chatrooms"."guid" = ?', array($GamingSession->ChatUUID))->first();
            //ADD OWNER TO CHAT
//            CometchatHelper::addToChatroom($GamingSession->ChatUUID, $GamingSession->MemberID);
            $chat = null;
            $GamingSession->write();
            
        } catch (Exception $e) {
            return false;
        }
        
        foreach($SourceGamingSession->getAttendees() as $attendee) {
            
            if(!empty($attendee->PushToken) && ($attendee->ID != $GamingSession->MemberID)) {
                    
                $Invitation = GamingSessionInvitation::create();
                $Invitation->Status = 'Pending';
                $Invitation->GamingSessionID = $GamingSession->ID;
                $Invitation->InvitationSenderID = CurrentUser::getUserID();
                $Invitation->InvitationRecipientID = $attendee->ID;
                if ($Invitation->write()) {
                    $push = new Fcm;
                    $push->send($attendee->PushToken, 'gamingsessioninvitation', [ 
                        'gamingsession_uid' => $GamingSession->UUID, 
                        'invitation_uid' => $Invitation->UUID,
                        'gamingsession_start_date' => (int)$GamingSession->dbObject('StartDate')->format('U'), 
                        'gamingsession_end_date' => (int)$GamingSession->dbObject('EndDate')->format('U'), 
                        'gamingsession_place_name' => $GamingSession->Venue()->exists() ? $GamingSession->Venue()->Location : $GamingSession->Location()->Location, 
                        'gamingsession_name' => $GamingSession->Name, 
                        'gamingsession_host' => $GamingSession->Member() ? $GamingSession->Member()->FirstName . ' ' . $GamingSession->Member()->Surname : '',
                        'gamingsession_chat_id' => $chat ? $chat['id'] : '',
                        'gamingsession_chat_name' => $chat ? $chat['name'] : '',
                    ], $GamingSession->Name, 'You have been invited to join a session.');
                }
            }
            
        }

        return true;
    }

}