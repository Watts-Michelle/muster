<?php

use Ramsey\Uuid\Uuid;

class CometchatHelper {
    
    public static function addChatroom($userID, $name, $type = 0){
        $fields = [
            'action' => 'creategroup',
            'groupname' => $name,
            'grouptype' => $type,
            'groupid' => Uuid::uuid4()->toString(),
            'userid' => $userID,
        ];
        return static::_call($fields, 'guid');
    }
    
    public static function addToChatroom($chatUuid, $users){
        $fields = [
            'action' => 'addgroupusers',
            'groupid' => $chatUuid,
            'users' => $users,
        ];
        
        return static::_call($fields, 'data');
    }
    
    public static function removeFromChatroom($chatUuid, $users){
        $fields = [
            'action' => 'removegroupusers',
            'groupid' => $chatUuid,
            'users' => $users,
        ];
        
        return static::_call($fields, 'data');
    }
    
    public static function sendInviteMessage($user, $name){
        $url = $_SERVER['HTTP_HOST'].'/cometchat/cometchat_send.php';
        $fields['api-key'] = COMETCHAT_API_KEY;
        $fields['to'] = CurrentUser::getUserID();
        $fields['message'] = 'has invited you to join group ' . $name;
        $fields['callbackfn'] = 'mobileapp';
        $fields['cc_sdk'] = '1';
        $fields['basedata'] = $user;
        $fields_string = '';
        foreach($fields as $key=>$value) { $fields_string .= $key.'='.($value).'&'; }
        $fields_string = rtrim($fields_string, '&');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$fields_string);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    
    private static function _call($fields, $importantField){
        $url = $_SERVER['HTTP_HOST'].'/cometchat/api/index.php';
        $fields['api-key'] = COMETCHAT_API_KEY;
        $fields_string = '';
        foreach($fields as $key=>$value) { $fields_string .= $key.'='.($value).'&'; }
        $fields_string = rtrim($fields_string, '&');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$fields_string);
        $result = curl_exec($ch);
        curl_close($ch);
        
        if (empty($result)) {
            return 0;
        }
        $result =  json_decode($result, true);
        
        if(isset($result['failed'])) {
            return 0;
        } else {
            return $result['success'][$importantField];
        }
    }
}