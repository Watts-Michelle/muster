<?php

class Fcm 
{

    private $_server_key = '';
    private $_server_url = '';
    private $_push_title = '';

    public function send($target, $type, $data, $title ='', $message = '', $useForeach = false)
    {
        
        if (empty(PUSH_SERVER_URL)) throw new \Exception('Missing PUSH_SERVER_URL in ss_environment');
        if (empty(PUSH_SERVER_KEY)) throw new \Exception('Missing PUSH_SERVER_KEY in ss_environment');
        if (empty(PUSH_DEFAULT_TITLE)) throw new \Exception('Missing PUSH_DEFAULT_TITLE in ss_environment');
        
        $this -> _server_key = PUSH_SERVER_KEY;
        $this -> _server_url = PUSH_SERVER_URL;
        $this -> _push_title = PUSH_DEFAULT_TITLE;
        
        $fields = array();
        $result = 0;

        $fields['notification']['title'] = !empty($title) ? $title : $this -> _push_title;
        $fields['notification']['body'] = !empty($message) ? $message: $this -> _push_title;

        if(is_array($target) && !$useForeach) {

            $fields['registration_ids'] = $target;
            
            foreach($target as $token) {
                $this->_addNotification(!empty($title) ? $title : $this -> _push_title, !empty($message) ? $message: $this -> _push_title, $token, $type, $data);
            }

            $result += $this->_makeCall($fields);

            
        } elseif(is_array($target)) {
            
            foreach($target as $token) {
                $fields['to'] = $token;
                $notObj = $this->_addNotification( !empty($title) ? $title : $this -> _push_title, !empty($message) ? $message: $this -> _push_title, $target, $type, $data);
                $fields['data']['notification_uid'] = $notObj->UUID;
                $result += $this->_makeCall($fields);
            }
            
        } else {
            
            $fields['to'] = $target;
            $notObj = $this->_addNotification( !empty($title) ? $title : $this -> _push_title, !empty($message) ? $message: $this -> _push_title, $target, $type, $data);
            $fields['data']['notification_uid'] = $notObj->UUID;
            $result += $this->_makeCall($fields);
        }
        
        return $result;

    }
    
    private function _addNotification($title, $message, $target, $type, $data) {
       $user = Member::get()->filter(['PushToken' => $target])->first();
       
       if($user) {
           
           $notification = new Notification();
           $notification->Title = $title;
           $notification->Message = $message;
           $notification->MemberID = $user->ID;
           $notification->StoredData = json_encode($data);
           $notification->Type = $type;
           if($notification->write()) {
               
               return $notification;
               
           } else {
               
               return false;
               
           }
           
       } else {
           
           return false;
           
       }
    }
    
    private function _makeCall($fields) {
        //header with content_type api key
        $headers = array(
            'Content-Type:application/json',
            'Authorization:key='.$this -> _server_key
        );
        
        //CURL request to route notification to FCM connection server (provided by Google)
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this -> _server_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);

        if ($result === FALSE)
        {
            die('Oops! FCM Send Error: ' . curl_error($ch));
        }

        switch ($ch['http_code'])
        {
            case 400:
                throw new O_HTTPResponse_Exception('Mallformed json or bad parameter type: '.$result, 400, 400);
                break;
            case 401:
                throw new O_HTTPResponse_Exception('There was an error authenticating the sender account.', 401, 401);
        }

        curl_close($ch);
        
        $result = json_decode($result, true);
        
        if(isset($result['success']) && $result['success']) {
           return true;
        } else {
           return false;
        }
    }
}