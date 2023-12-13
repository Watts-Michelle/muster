<?php

class Chat_Controller extends Api_Controller {

    private static $allowed_actions = array(
        'CurrentUser',
        'getUserMembers',
        'getUserRejectedMembers',
        'getChatImage',
        'messageRequestSend',
        'messageRequestAccept',
        'messageRequestReject'
    );

    private static $url_handlers = array(
        '' => 'CurrentUser',
        '$UID/members' => 'getUserMembers',
        '$UID/rejected' => 'getUserRejectedMembers',
        '$UID/image' => 'getChatImage',
        '$UID/messagerequest/$userUID/send' => 'messageRequestSend',
        '$UID/messagerequest/$userUID/accept' => 'messageRequestAccept',
        '$UID/messagerequest/$requesterUID/reject' => 'messageRequestReject'
    );

    public function CurrentUser(SS_HTTPRequest $request) {
        return '';
    }
    
    public function getChatImage(SS_HTTPRequest $request)
    {
        $chatID = $request ->param('UID');
        
        $session = GamingSession::get() -> filter(array('ChatUUID' => $chatID)) ->first() ;
        
        if($session)
        {
            return (new JsonApi)->formatReturn(['image' => $session -> getGamingSessionImage()]);
        }
        
        return $this->handleError(404, 'TO DO: Wrong UID or no related session!!');
    }
    
    public function getUserMembers(SS_HTTPRequest $request)
    {
        if (!$request->isGET()) return $this->handleError(404, 'Must be a GET request');

        if (!($UID = $request->param('UID'))) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if ($UID != CurrentUser::getUserUUID()) {
            return $this->handleError(404, 'TO DO: Your user does not match the currently logged in user!');
        }
        
        $members_ids = [];
        $members = [];
        $members_data = [];
        $friends = CurrentUser::getUser()->getFriends();
        
        $message_requests = MessageRequest::get() ->filter(['Accepted' => 1])
                ->filterAny(['RequesterID' => CurrentUser::getUserID(), 'RecieverID' => CurrentUser::getUserID()]);
        
        
        foreach($friends as $friend)
        {   
            $members_ids[] = $friend['member_id'];
        }
        
        foreach($message_requests as $request)
        {
            $members_ids[] = $request -> RequesterID;
            $members_ids[] = $request -> RecieverID;
        }
      
        $members_ids = array_unique($members_ids);
        $members_ids = array_diff( $members_ids, [CurrentUser::getUserID()] );

        foreach(Member::get() -> filter('ID', $members_ids) as $member)
        {
            $members_data[] = $member -> getData();
        }
        
        return (new JsonApi)->formatReturn(['count' => count($members_data), 'users' => $members_data]);
    }
    
    public function getUserRejectedMembers(SS_HTTPRequest $request)
    {
        if (!$request->isGET()) return $this->handleError(404, 'Must be a GET request');

        if (!($UID = $request->param('UID'))) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if ($UID != CurrentUser::getUserUUID()) {
            return $this->handleError(404, 'TO DO: Your user does not match the currently logged in user!');
        }
        
        $members_ids = [];
        $friend_ids = [];
        $members = [];
        $members_data = [];
        $friends = CurrentUser::getUser()->getFriends();
        
        $message_requests = MessageRequest::get() ->filter(['Accepted' => -1, 'RecieverID' => CurrentUser::getUserID()]);
        
        
        foreach($friends as $friend)
        {   
            $friend_ids[] = $friend['member_id'];
        }
        
        foreach($message_requests as $request)
        {
            $members_ids[] = $request -> RequesterID;
            $members_ids[] = $request -> RecieverID;
        }
      
        $members_ids = array_unique($members_ids);
        $members_ids = array_diff( $members_ids, [CurrentUser::getUserID()], $friend_ids );

        foreach(Member::get() -> filter('ID', $members_ids) as $member)
        {
            $members_data[] = $member -> getData();
        }
        
        return (new JsonApi)->formatReturn(['count' => count($members_data), 'users' => $members_data]);
    }
    
    public function messageRequestSend(SS_HTTPRequest $request)
    {
        if (!$request->isPOST()) return $this->handleError(404, 'Must be a POST request');

        if (!($UID = $request->param('UID') && $request -> param('userUID') )) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if ($UID != CurrentUser::getUserUUID()) {
            return $this->handleError(404, 'TO DO: Your user does not match the currently logged in user!');
        }
        
        if(!($requester = $this->checkUuidExists($request->param('UID'), 'Member')) || !($reciever = $this->checkUuidExists($request->param('userUID'), 'Member'))) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }
        
        if(MessageRequest::get() -> filter(array('RequesterID' => $requester -> ID, 'RecieverID' => $reciever -> ID  )) -> count() )
        {
            return $this->handleError(409, 'You have a message request already send for this user !', 409);
        }
        
        if(MessageRequest::get() -> filter(array('RequesterID' => $reciever -> ID, 'RecieverID' =>  $requester -> ID  )) -> count() )
        {
            return $this->handleError(408, 'You have a message request pending from this user, accept it !!', 408);
        }
        
        //Users_Controller :1367
        $messageRequest = MessageRequest::create();
        $messageRequest -> RequesterID = $requester -> ID;
        $messageRequest -> RecieverID = $reciever -> ID;
        $status = $messageRequest -> write();
        
        if(!empty($reciever->PushToken)) {
            $push = new Fcm;
            $push->send($reciever->PushToken, 'message_request', 
                    [
                        'friend_uid' => $requester->UUID, 
                        'friend_name' =>  $requester->FirstName.' '.$requester->Surname,
                        'friend_profileImage' =>  $requester->getUserImage(),
                    ],
                    $requester->FirstName.' '.$requester->Surname.' sent you a message request.');
        }
        
        
        return (new JsonApi)->formatReturn(['success' => $status]);
    }
    
    public function messageRequestAccept(SS_HTTPRequest $request)
    {
        if (!$request->isPUT()) return $this->handleError(404, 'Must be a PUT request');

        if (!($UID = $request->param('UID') && $request -> param('userUID') )) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if ($UID != CurrentUser::getUserUUID()) {
            return $this->handleError(404, 'TO DO: Your user does not match the currently logged in user!');
        }
        
        if(!($reciever = $this->checkUuidExists($request->param('UID'), 'Member')) || !($requester = $this->checkUuidExists($request->param('userUID'), 'Member'))) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }
        
        if(! $msgRequest = MessageRequest::get() -> filter(array('RequesterID' => $requester -> ID, 'RecieverID' => $reciever -> ID  )) -> first())
        {
            return $this->handleError(404, 'No messge request from that user !!');
        }
        
        $msgRequest -> Accepted = 1;
        $status = $msgRequest -> write();
        
        return (new JsonApi)->formatReturn(['success' => $status]);
    }
    
    public function messageRequestReject(SS_HTTPRequest $request)
    {
        if (!$request->isPUT()) return $this->handleError(404, 'Must be a PUT request');
        
        if (!(($UID = $request->param('UID')) && $request -> param('requesterUID') )) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }
        
        if ($UID != CurrentUser::getUserUUID()) {
            return $this->handleError(404, 'TO DO: Your user does not match the currently logged in user!');
        }
                
        if(!(($reciever = $this->checkUuidExists($request->param('UID'), 'Member')) && $requester = $this->checkUuidExists($request->param('requesterUID'), 'Member'))) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }
        
        if(! $msgRequest = MessageRequest::get() -> filter(array('RequesterID' => $requester -> ID, 'RecieverID' => $reciever -> ID  )) -> first())
        {
            return $this->handleError(404, 'No messge request from that user !!');
        }
        
        $msgRequest -> Accepted = -1;
        $status = $msgRequest -> write();
        
        return (new JsonApi)->formatReturn(['success' => $status]);
    }
    

}