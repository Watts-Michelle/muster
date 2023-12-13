<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/

include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."modules.php");

if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php")) {
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."lang.php");
}

if ($_REQUEST['basedata']) {
	$basedata = rawurlencode($_REQUEST['basedata']);
} else {
	$basedata = null;
}

if(empty($_REQUEST['force'])&&!empty($_REQUEST['f'])){
	$_REQUEST['force'] = $_REQUEST['f'];
}

$crtimestamp = 0;
if (!empty($_REQUEST['crtimestamp'])) {
	$crtimestamp = $_REQUEST['crtimestamp'];
}elseif(!empty($_REQUEST['timestamp']) && $currentversion<'6.3.0'){
	$crtimestamp = $_REQUEST['timestamp'];
}

$embed = '';
$embedcss = '';
$close = "setTimeout('closePopup();',2000);";

if(empty($response)){
	$response = array();
}

$joinedrooms = array();
if (!empty($_GET['embed']) && $_GET['embed'] == 'web') {
	$embed = 'web';
	$embedcss = 'embed';
	$close = "setTimeout('closePopup();',2000);";
}

if (!empty($_GET['embed']) && $_GET['embed'] == 'desktop') {
	$embed = 'desktop';
	$embedcss = 'embed';
	$close = "parentSandboxBridge.closeCCPopup('invite');";
}

if ($userid == 0 || in_array($userid,$bannedUserIDs)) {
	$response['logout'] = 1;
	$response['loggedout'] = 1;
	if(!empty($_GET['action'])) {
		header('Content-type: application/json; charset=utf-8');
		if (!empty($_GET['callback'])) {
			echo $_GET['callback'].'('.json_encode($response).')';
		} else {
			echo json_encode($response);
		}
		exit;
	}
}elseif(!empty($_REQUEST['initialize'])){
	$response['userid'] = $userid;
}
if(!empty($_REQUEST['action']) && $_REQUEST['action'] == 'sendmessage'){
	$_REQUEST['action'] = 'sendChatroomMessage';
}

if(!empty($_REQUEST['action']) && $_REQUEST['action'] == 'updateChatroomMessages'){
	global $lastMessages;
	getChatroomData($_REQUEST['chatroomid'], $_REQUEST['prepend'], $lastMessages);
}
if(!empty($_REQUEST['action']) && $_REQUEST['action'] == 'getChatroomName'){
	$getId = $_REQUEST['id'];
	if(!empty($getId)){
		$sql = sql_getQuery('getRoomName',array('chatroomid'=>$getId));
		$query = sql_query($sql, array(), 1);
		$result = sql_fetch_assoc($query);
		$response['name'] = $result['name'];
		if (!empty($_GET['callback'])) {
			echo $_GET['callback'].'('.json_encode($response).')';
		} else {
			echo json_encode($response);
		}
		exit;
	}
}

function heartbeat() {
	global $response;
	global $userid;
	global $chatrooms_language;
	global $chatroomTimeout;
	global $lastMessages;
	global $cookiePrefix;
	global $allowAvatar;
	global $moderatorUserIDs;
	global $guestsMode, $crguestsMode, $guestnamePrefix;
	global $chromeReorderFix;
	global $showChatroomUsers;
	global $joinedrooms;
	global $showGroupsOnlineUsers;

	$usertable = TABLE_PREFIX.DB_USERTABLE;
	$usertable_username = DB_USERTABLE_NAME;
	$usertable_userid = DB_USERTABLE_USERID;

	$time = getTimeStamp();
	$chatroomList = array();
	$force = 0;
	if(!empty($_REQUEST['force'])){
		$force = $_REQUEST['force'];
	}
	if(!empty($_REQUEST['initialize']) && $_REQUEST['initialize'] == '1'){
		unset($_SESSION['cometchat']['cometchat_joinedchatroomids']);
		$force = 1;
	}

	if($force == 1 && empty($_SESSION['cometchat']['cometchat_joinedchatroomids'])){
		$joinedChatroomIds = array();
		$query = sql_query('getJoinedGroups',array('userid'=>$userid));
		if (defined('DEV_MODE') && DEV_MODE == '1') { echo sql_error($GLOBALS['dbh']); }
		while ($result = sql_fetch_assoc($query)) {
			$joinedChatroomIds[] = $result['id'];
		}
		$_SESSION['cometchat']['cometchat_joinedchatroomids'] = $joinedChatroomIds;
	}

	if(isset($_SESSION['cometchat']['cometchat_joinedchatroomids'])){
		$joinedrooms = $_SESSION['cometchat']['cometchat_joinedchatroomids'];
	}

	if (empty($_SESSION['cometchat']['cometchat_lastlactivity']) || ($time-$_SESSION['cometchat']['cometchat_lastlactivity'] >= REFRESH_BUDDYLIST/4)) {
		$sql = updateLastActivity($userid);
		if (function_exists('hooks_updateLastActivity')) {
			hooks_updateLastActivity($userid);
		}
		$query = sql_query($sql, array(), 1);

		if (defined('DEV_MODE') && DEV_MODE == '1') { echo sql_error($GLOBALS['dbh']); }
		$_SESSION['cometchat']['cometchat_lastlactivity'] = $time;
	}

	if ((empty($_SESSION['cometchat']['cometchat_chatroomslist'])) || $force==1 || (!empty($_SESSION['cometchat']['cometchat_chatroomslist']) && ($time-$_SESSION['cometchat']['cometchat_chatroomslist'] > REFRESH_BUDDYLIST))) {

		if(!is_array($cachedChatrooms = getCache('chatroom_list'))|| ($force==1)) {
			$cachedChatrooms = array();

			if($showGroupsOnlineUsers == 1){
				$sqlPart = "and ((cometchat_status.lastactivity > (".mysqli_real_escape_string($GLOBALS['dbh'],$time)."-".((ONLINE_TIMEOUT)*2).")) OR cometchat_status.isdevice = 1) and (cometchat_status.status IS NULL OR cometchat_status.status <> 'invisible' OR cometchat_status.status <> 'offline')";
			} else {
				$sqlPart = "";
			}
			if($showChatroomUsers == 1){
				$sqlPart = sql_getQuery('groups_sqlpart',array('timestampCondition'=>$sqlPart));
			} else {
				$sqlPart = '0';
			}

			$sql = sql_getQuery('getGroupsData',array('sqlpart'=>$sqlPart));
			/* hooks for group list*/
			if (function_exists('hooks_getGroupList')) {
				$sql = hooks_getGroupList(array('sqlPart'=>$sqlPart));
			}

			$query = sql_query($sql,array(),1);
			while ($chatroom = sql_fetch_assoc($query)) {
				$cachedChatrooms[$chromeReorderFix.$chatroom['id']] = array('id' => $chatroom['id'], 'name' => urldecode($chatroom['name']), 'online' => $chatroom['members'], 'members' => $chatroom['members'], 'type' => $chatroom['type'], 'password' => $chatroom['password'], 'lastactivity' => $chatroom['lastactivity'], 'createdby' => $chatroom['createdby'], 'invitedusers' => $chatroom['invitedusers']);
			}
			setCache('chatroom_list',$cachedChatrooms,30);
		}

		foreach($cachedChatrooms as $key=>$chatroom) {
			if((($chatroom['createdby'] == 0 || ($chatroom['createdby'] <> 0 && $time - $chatroom['lastactivity'] < $chatroomTimeout)) || $chatroom['createdby'] == $userid) && ($chatroom['type'] <> 3)) {
				$userList = explode(',', $chatroom['invitedusers']);
				$s = 0;
				if ($chatroom['createdby'] != $userid) {
					if(in_array($userid,$moderatorUserIDs)){
						$s = 2;
					}
				} else {
					$s = 1;
				}
				if($chatroom['type'] == 2 && !in_array($userid, $userList) && $chatroom['createdby'] != $userid){
					continue;
				} else {
					$joined = 0;
					if(in_array($chatroom['id'], $joinedrooms)){
						$joined = 1;
					}
					$chatroomList[$chromeReorderFix.$chatroom['id']] = array('id' => $chatroom['id'], 'name' => $chatroom['name'], 'online' => $chatroom['online'], 'type' => $chatroom['type'], 'i' => $chatroom['password'], 's' => $s, 'createdby' => $chatroom['createdby'], 'j' => $joined);
				}
			}
		}

		$_SESSION['cometchat']['cometchat_chatroomslist'] = $time;

		$clh = md5(serialize($chatroomList));
		if((empty($_REQUEST['clh'])) || (!empty($_REQUEST['clh']) && $clh != $_REQUEST['clh']) || ($force == 1)) {
			$response['chatrooms'] = $chatroomList;
			$response['clh'] = $clh;
		}
	}


	if(!empty($_REQUEST['initialize']) && $_REQUEST['initialize']==1 && !empty($joinedrooms) || (!empty($_REQUEST['currentroom']) && $force == 1 && USE_COMET == 1 && COMET_CHATROOMS == 1)){
		$LastMessageIdList = array();
		$implodedChatrooms = implode(',',$joinedrooms);

		if(!empty($implodedChatrooms)){
			$query = sql_query('getGroupMsgMaxIds',array('implodedChatrooms'=>$implodedChatrooms));
			while ($result = sql_fetch_assoc($query)) {
				$LastMessageIdList[$chromeReorderFix.$result['chatroomid']] = $result['id'];
				if(!isset($crreadmessages[$result['chatroomid']])){
					$crreadmessages[$result['chatroomid']] = $result['id'];
				}
			}
		}


		if (!empty($LastMessageIdList)) {
			$response['chatroomList'] = $LastMessageIdList;
		}

		if (USE_COMET == 1 && COMET_CHATROOMS == 1) {
			$cometresponse = array();
			foreach($joinedrooms as $key => $chatroomid){
				$key = '';
				if( defined('KEY_A') && defined('KEY_B') && defined('KEY_C') ){
					$key = KEY_A.KEY_B.KEY_C;
				}
				$cometresponsedata = array(
					'chatroomid' => $chatroomid,
					'cometid' => md5('chatroom_'.$chatroomid.$key),
					'userid' => $userid
				);
				array_push($cometresponse, $cometresponsedata);
			}
			$response['subscribeChatrooms'] = $cometresponse;
		}
	}

	$sql = '';
	if(!empty($_REQUEST['currentroom'])){
		$sql = sql_getQuery('getGroupPassword',array('currentroom'=>$_REQUEST['currentroom']));
	}

	fetchChatroomMessages(array('joinedrooms'=>$joinedrooms,'force'=>$force));

	if($sql && $_REQUEST['currentroom'] > 0){
		$query = sql_query($sql, array(), 1);
		if($room = sql_fetch_assoc($query)){
			if (!empty($room['password']) && (empty($_REQUEST['currentp']) || ($room['password'] != $_REQUEST['currentp']))) {
				$response['users'] = array();
				$response['messages'] = array();
			}
		}
	}

	if(function_exists('hooks_groupHeartbeat')){
		hooks_groupHeartbeat(array());
	}

	if(!isset($_REQUEST['crtimestamp']) && !isset($_REQUEST['crinitialize']) && !empty($_REQUEST['callbackfn']) && $_REQUEST['callbackfn']=='mobileapp'){
		if (!empty($_GET['callback'])) {
			echo $_GET['callback'].'('.json_encode($response).')';
		}
	}
}

function fetchChatroomMessages($params){
	global $response, $userid, $lastMessages, $guestsMode, $crguestsMode, $crtimestamp, $chatrooms_language, $cookiePrefix, $guestnamePrefix, $trayicon;

	$joinedrooms = $params['joinedrooms'];
	$force = $params['force'];

	$crreadmessages = array();
	if(isset($_REQUEST['crreadmessages'])){
		$crreadmessages = $_REQUEST['crreadmessages'];
	}

	if(!empty($_REQUEST['v']) && !empty($crreadmessages)){
		$crreadmessages = json_decode($crreadmessages, true);
	}
	if(!empty($joinedrooms) && is_array($crreadmessages) && !empty($crreadmessages)){
		foreach($crreadmessages as $chatroomid => $unreadMessages){
			if(!in_array($chatroomid, $joinedrooms)){
				unset($crreadmessages[$chatroomid]);
			}
		}
	}

	if (count($joinedrooms) > 0 || (!empty($_REQUEST['callbackfn']) && $_REQUEST['callbackfn']=='mobileapp')) {
		$messages = array();
		$moremessages = array();

		if(!empty($_REQUEST['callbackfn']) && !empty($_REQUEST['currentroom']) && $_REQUEST['callbackfn']=='mobileapp' && (empty($_REQUEST['appinfo']) || (!empty($_REQUEST['appinfo']) && $_REQUEST['appinfo']['v'] < '6.3.0'))){
			$response = array_merge(getchatroomusers(),$response);
		}
		$limit = $lastMessages;

		if(is_array($crreadmessages) && !empty($crreadmessages)){
			foreach($crreadmessages as $key => $value){
				if(!in_array($key, $joinedrooms)){
					unset($crreadmessages[$key]);
				}
			}
		}

		if(!empty($_POST['currentroom']) && $force == 1 && !empty($_SESSION['cometchat']['cometchat_chatroom_'.$_REQUEST['currentroom']])){
			$messages = getChatroomData($_REQUEST['currentroom'],0,10);
			$messages = array_reverse($messages);
		} else {
			if (USE_COMET == 1 && empty($_REQUEST['initialize']) && $force != 1) { return; }
			$guestpart = "";
			$limitClause = " limit ".sql_real_escape_string($limit)." ";
			$timestampCondition = "";

			foreach ($crreadmessages as $chatroomid => $lastmessageid) {
				if(((!empty($_REQUEST['initialize']) && $_REQUEST['initialize']==1) || (!empty($lastmessageid) && $force == 1)) && (USE_COMET == 1 && COMET_CHATROOMS == 1)){
					$lastmessageid = $lastmessageid-$lastMessages;
				}
				if(!empty($_SESSION['cometchat']['chatrooms_'.$chatroomid.'_clearId']) && empty($_SESSION['cometchat']['cometchat_chatroom_'.$chatroomid])){
 					$timestampCondition .= sql_getQuery('group_timestampcondition1',array('chatroomid'=>$chatroomid, 'id'=>$_SESSION['cometchat']['chatrooms_'.$chatroomid.'_clearId']));
 				}else{
					$timestampCondition .= sql_getQuery('group_timestampcondition2',array('chatroomid'=>$chatroomid, 'id'=>$lastmessageid));
 				}
			}
			if(count($crreadmessages) > 0){
				$timestampCondition = rtrim($timestampCondition,"or");
				$timestampCondition = $timestampCondition." and ";
				$limitClause = '';
			}
			if((!empty($_REQUEST['callbackfn']) && $_REQUEST['callbackfn']=='mobileapp' && empty($_REQUEST['v'])) || (!empty($joinedrooms) && empty($_REQUEST['v']))){
				$timestampCondition = "";
				if ($crtimestamp != 0) {
					$timestampCondition = sql_getQuery('group_timestampcondition3',array('joinedrooms'=>implode(",", $joinedrooms), 'id'=>$crtimestamp));
					$limitClause = "";
				} else {

					$timestampCondition = sql_getQuery('group_timestampcondition4',array('joinedrooms'=>implode(",", $joinedrooms)));
					$limitClause = sql_getQuery('groups_guestpart_limitClause',array('limit'=>$limit));
				}
			}

			if ($guestsMode && $crguestsMode) {
				$guestpart = sql_getQuery('groups_guestpart',array('guestnamePrefix'=>$guestnamePrefix, 'timestampCondition'=>$timestampCondition, 'limitClause'=>$limitClause));
			}

			if (!empty($crreadmessages) || !empty($_REQUEST['currentroom'])){
				$query = sql_query('groupMessages',array('timestampCondition'=>$timestampCondition, 'guestpart'=>$guestpart, 'limitClause'=>$limitClause));

				if(sql_num_rows($query) > 0) {
					while ($chat = sql_fetch_assoc($query)) {
						if (function_exists('processName')) {
							$chat['from'] = processName($chat['from']);
						}

						if ($lastMessages == 0 && $crtimestamp == 0) {
							$chat['message'] = '';
						}

						if ($userid == $chat['userid']) {
							$chat['from'] = $chatrooms_language[6];
						} else {
							/*START: Backward Compatibility for Mobileapp*/
							if(!empty($_REQUEST['callbackfn']) && $_REQUEST['callbackfn']=='mobileapp'){
								if (!empty($trayicon['realtimetranslate']) && (!empty($_REQUEST[$cookiePrefix.'rttlang']) || !empty($_COOKIE[$cookiePrefix.'rttlang'])) && strpos($chat['message'],'CC^CONTROL_') === false) {
									if(!empty($_REQUEST[$cookiePrefix.'rttlang'])){
										$translated = text_translate($chat['message'],'',$_REQUEST[$cookiePrefix.'rttlang']);
									}
									if(!empty($_COOKIE[$cookiePrefix.'rttlang'])){

										$translated = text_translate($chat['message'],'',$_COOKIE[$cookiePrefix.'rttlang']);
									}
									if ($translated != '') {
										$chat['message'] = strip_tags($translated).' ('.$chat['message'].')';
									}
								}
							}
							/*END: Backward Compatibility for Mobileapp*/
						}

						$localmessageid = 0;
						if(!empty($_SESSION['cometchat']['duplicates']['group_localmessageid'])){
							$localmessageid = array_search($chat['id'], $_SESSION['cometchat']['duplicates']['group_localmessageid']);
						}

						array_unshift($messages,array('id' => $chat['id'], 'from' => $chat['from'], 'chatroomid' => $chat['chatroomid'], 'fromid' => $chat['fromid'], 'message' => $chat['message'], 'sent' => ($chat['sent']), 'localmessageid' => $localmessageid));

						$_SESSION['cometchat']['cometchat_chatroom_'.$chat['chatroomid']][$chat['id']] = array('id' => $chat['id'], 'chatroomid' => $chat['chatroomid'], 'from' => $chat['from'],'fromid' => $chat['fromid'], 'message' => $chat['message'], 'sent' => ($chat['sent']), 'localmessageid' => $localmessageid);
					}
				}
			}

		}

		if (!empty($messages)) {
			$response['messages'] = $messages;
		}
	}
}

function createchatroom() {

	global $userid;
	$name = $_REQUEST['name'];
	$name = str_replace("%27","'", $name);

	$password = $_REQUEST['password'];
	$type = $_REQUEST['type'];

	$query = sql_query('getGroupName',array('name'=>$name));
	if(sql_num_rows($query) == 0) {
		if ($userid > 0) {
			$time = getTimeStamp();
			if (!empty($password)) {
				$password = sha1($password);
			} else {
				$password = '';
			}

			$query = sql_query('insertChatroom',array('name'=>sanitize_core($name), 'createdby'=>$userid, 'lastactivity'=>getTimeStamp(), 'password'=>sanitize_core($password), 'type'=>sanitize_core($type)));
			$currentroom = sql_insert_id('cometchat_chatrooms');

			$query = sql_query('insertChatroomUser',array('userid'=>$userid, 'chatroomid'=>$currentroom));
			//Store chatroom name in session for push notifications
			$_SESSION['cometchat']['chatrooms']=array('_'.$currentroom=>array('id'=>$currentroom,'name'=>$name));
			$_SESSION['cometchat']['chatroom']['n'] = sanitize_core(base64_encode(urldecode($_REQUEST['name'])));
			$_SESSION['cometchat']['chatroom']['id'] = $currentroom;

			/*hooks create group*/
			if (function_exists('hooks_createGroup')){
				hooks_createGroup(array('chatroomid'=>$currentroom));
			}

			header('Content-type: application/json; charset=utf-8');
			if (!empty($_GET['callback'])) {
				echo $_GET['callback'].'('.json_encode($_SESSION['cometchat']['chatroom']).')';
			} else {
				echo json_encode($_SESSION['cometchat']['chatroom']);
			}
			exit(0);
		}
	} else {
		if (!empty($_GET['callback'])) {
			echo $_GET['callback'].'('.json_encode(0).')';
		} else {
			echo json_encode(0);
		}
		exit;
	}
}

function getchatroomusers() {
	global $userid;
	global $guestsMode;
	global $crguestsMode;
	global $allowAvatar;
	global $chromeReorderFix;
	global $force;
	global $showGroupsOnlineUsers;

	$response = array();
	$users = array();
	$time = getTimeStamp();

	if(!is_array($users = getCache('chatrooms_users'.$_REQUEST['currentroom'])) || ($force == 1)) {
		if($showGroupsOnlineUsers == 1){
			$sqlPart = "and ((cometchat_status.lastactivity > (".mysqli_real_escape_string($GLOBALS['dbh'],$time)."-".((ONLINE_TIMEOUT)*2).")) OR cometchat_status.isdevice = 1) and (cometchat_status.status IS NULL OR cometchat_status.status <> 'invisible' OR cometchat_status.status <> 'offline')";
		} else {
			$sqlPart = "";
		}
		$sql = sql_getQuery('getchatroomusers',array('chatroomid'=>$_REQUEST['currentroom'],'timestampCondition'=>$sqlPart));
		if($guestsMode && $crguestsMode){
			$sql = getChatroomGuests($_REQUEST['currentroom'],$time,$sql);
		}
		$query = sql_query($sql, array(), 1);

		while ($chat = sql_fetch_assoc($query)) {
			if (function_exists('processName')) {
				$chat['username'] = processName($chat['username']);
			}
			$avatar = '';
			if($allowAvatar) {
				$avatar = getAvatar($chat['avatar']);
			}

			$users[$chromeReorderFix.$chat['userid']] = array('id' => (int)$chat['userid'], 'n' => $chat['username'], 'a' => $avatar, 'b' => $chat['isbanned'], 'chatroomid' => $_REQUEST['currentroom']);
		}
		setCache('chatrooms_users'.$_REQUEST['currentroom'],$users,30);
	}

	if (empty($_SESSION['cometchat']['cometchat_chatroom_'.$_REQUEST['currentroom']])) {
		$_SESSION['cometchat']['cometchat_chatroom_'.$_REQUEST['currentroom']] = array();
	}

	$ulh = md5(serialize($users));
	if((empty($_REQUEST['ulh'])) || (!empty($_REQUEST['ulh']) && $ulh !== $_REQUEST['ulh'])) {
		if (!empty($users)) {
			$response['ulh'] = $ulh;
			$response['users'] = $users;
		}
	}
	if(!empty($_REQUEST['action']) && $_REQUEST['action']=='getchatroomusers'){
		header('Content-type: application/json; charset=utf-8');
		if (!empty($_GET['callback'])) {
			echo $_GET['callback'].'('.json_encode($response).')';
		} else {
			echo json_encode($response);
		}
		exit;
	}

	return $response;
}

function deletechatroom(){
	global $userid;
	global $moderatorUserIDs;
	global $cookiePrefix;

	$createdby = " and createdby != 0 ";

	if (!empty($_REQUEST['id'])) {
		if(!in_array($userid, $moderatorUserIDs)){
			$createdby .= " and createdby = ".sql_real_escape_string(sanitize_core($userid))." ";
		}
		$joinedChatroomIds = $_SESSION['cometchat']['cometchat_joinedchatroomids'];
		$key = array_search($_REQUEST['id'],$joinedChatroomIds);
		if($key!==false){
			unset($joinedChatroomIds[$key]);
		}
		$_SESSION['cometchat']['cometchat_joinedchatroomids'] = $joinedChatroomIds;

		$controlparameters = array('type' => 'modules', 'name' => 'chatroom', 'method' => 'deletedchatroom', 'params' => array('id' => $_REQUEST['id']));
		$controlparameters = json_encode($controlparameters);
		$msgresponse = sendChatroomMessage($_REQUEST['id'],'CC^CONTROL_'.$controlparameters,0);
		$query = sql_query('deleteGroup_messages',array('id'=>sanitize_core($_REQUEST['id'])));
		$query = sql_query('deleteGroup',array('id'=>sanitize_core($_REQUEST['id']), 'createdby'=>$createdby));
		$affectedrow =  sql_affected_rows($query);
		$msgresponse .= '_'.time();
		if (!empty($_GET['callback'])) {
			if($affectedrow>0 && !isset($_REQUEST['callbackfn'])){
				echo $_GET['callback'].'('.json_encode($msgresponse).')';
			} else {
				echo $_GET['callback'].'('.json_encode($affectedrow).')';
			}
		} else {
			echo json_encode($affectedrow);
		}
		exit;
	}
	removeCache('chatroom_list');
	if (!empty($_GET['callback'])) {
		echo $_GET['callback'].'('.json_encode(0).')';
	} else {
		echo json_encode(0);
	}
	exit;
}

function renamechatroom(){
	global $userid;
	if(!empty($_REQUEST['id']) && !empty($_REQUEST['cname'])){
		$query = sql_query('getChatrooms',array('name'=>$_REQUEST['cname']));
		if(sql_num_rows($query) >= 1) {
			if (!empty($_GET['callback'])) {
				echo $_GET['callback'].'('.json_encode(0).')';
			} else {
				echo json_encode(0);
			}
			exit;
		}

		if(!empty($_REQUEST['cc_sdk'])){
			$query = sql_query('getChatroomById',array('id'=>$_REQUEST['id']));
			$row = sql_fetch_assoc($query);
			if(!($userid == $row['createdby'] || in_array($userid, $moderatorUserIDs))){
				if (!empty($_GET['callback'])) {
					echo $_GET['callback'].'('.json_encode(2).')';
				} else {
					echo json_encode(2);
				}
				exit;
			}
		}

		$query = sql_query('renameGroup',array('name'=>rawurldecode($_REQUEST['cname']), 'id'=>$_REQUEST['id']));
		if (!empty($_GET['callback'])) {
			echo $_GET['callback'].'('.json_encode(1).')';
		} else {
			echo json_encode(1);
		}
		exit;
	}
	exit;
}

function checkpassword() {
	global $userid;
	global $cookiePrefix;
	global $moderatorUserIDs;
	global $channelprefix;
	global $pushplatformsuffix;
	$response = array();
	$chatroomname = '';
	$_SESSION['cometchat']['isModerator'] = 0;
	$id = sql_real_escape_string($_REQUEST['id']);
	$joinedChatroomIds = array();

	if(!empty($_SESSION['cometchat']['cometchat_joinedchatroomids'])) {
		$joinedChatroomIds = $_SESSION['cometchat']['cometchat_joinedchatroomids'];
	}

	if((!empty($_REQUEST['type']) && $_REQUEST['type']==1) && empty($_REQUEST['silent']) && !in_array($id, $joinedChatroomIds)) {
		$response['s'] = 'REQUIRED_PASSWORD';
		header('Content-type: application/json; charset=utf-8');
		if (!empty($_GET['callback'])) {
			echo $_GET['callback'].'('.json_encode($response).')';
		} else {
			echo json_encode($response);
		}
		exit;
	}
	if(!empty($_REQUEST['password'])) {
		$password = sql_real_escape_string($_REQUEST['password']);
	}

	$query = sql_query('checkchatroombanneduser',array('userid'=>$userid, 'chatroomid'=>$id));
	if(sql_num_rows($query) == 1){
		$response['s'] = 'BANNED';
		$responseLeg = 2;

		if(!empty($_REQUEST['callbackfn']) && $_REQUEST['callbackfn']<>'mobileapp'){
			echo $responseLeg;
		} else{
			header('Content-type: application/json; charset=utf-8');
			if (!empty($_GET['callback'])) {
				echo $_GET['callback'].'('.json_encode($response).')';
			} else {
				echo json_encode($response);
			}
		}
		exit;
	}
	if ($userid > 0) {
		$query = sql_query('getChatroomById',array('id'=>$_REQUEST['id']));
		if($room = sql_fetch_assoc($query)){
			if (!empty($room['password']) && (empty($_REQUEST['password']) || ($room['password'] != $_REQUEST['password']))) {
				$response['s'] = 'INVALID_PASSWORD';
				$responseLeg = "0";

				if(!empty($_REQUEST['callbackfn']) && $_REQUEST['callbackfn']<>'mobileapp'){
					echo $responseLeg;
				} else{
					header('Content-type: application/json; charset=utf-8');
					if (!empty($_GET['callback'])) {
						echo $_GET['callback'].'('.json_encode($response).')';
					} else {
						echo json_encode($response);
					}
				}
				exit;

			} else {
				removeCache('chatrooms_users'.$id);
				removeCache('chatroom_list');

				$query = sql_query('deleteKickedMessage',array('chatroomid'=>$id, 'userid'=>$userid));
				$query = sql_query('unbanChatroomUser',array('chatroomid'=>$id, 'userid'=>$userid));
				if ($room['createdby'] == $userid || in_array($userid,$moderatorUserIDs)) {
					$_SESSION['cometchat']['isModerator'] = 1;
				}
				$key = '';
				if( defined('KEY_A') && defined('KEY_B') && defined('KEY_C') ){
					$key = KEY_A.KEY_B.KEY_C;
				}

				$response=array('s' => 'JOINED',
					'chatroomname' => $room['name'],
					'timestamp' => 0,
					'cometid' => md5('chatroom_'.$id.$key),
					'owner' => ($room['createdby'] == $userid?"1":"0"),
					'userid' => $userid,
					'ismoderator' => $_SESSION['cometchat']['isModerator'],
					'push_channel' => 'C_'.md5($channelprefix."CHATROOM_".$id.BASE_URL).getPlatformSuffix($pushplatformsuffix)
					);
				$responseLeg = md5('chatroom_'.$id.$key)."^".($room['createdby'] == $userid?"1":"0")."^".$userid."^".$_SESSION['cometchat']['isModerator'];
				//Store chatroom name in session for push notifications
				$_SESSION['cometchat']['chatrooms']=array('_'.$id=>array('id'=>$id,'name'=>$room['name']));
				$_SESSION['cometchat']['chatroom']['n'] = base64_encode($room['name']);
				$_SESSION['cometchat']['chatroom']['id'] = $id;

				if(!in_array($id, $joinedChatroomIds)) {
					array_push($joinedChatroomIds,$id);
					$_SESSION['cometchat']['cometchat_joinedchatroomids'] = $joinedChatroomIds;
				}
			}
		}
		if(!empty($_REQUEST['callbackfn']) && $_REQUEST['callbackfn']<>'mobileapp'){
			echo $responseLeg;
		} else{
			header('Content-type: application/json; charset=utf-8');
			if (!empty($_GET['callback'])) {
				echo $_GET['callback'].'('.json_encode($response).')';
			} else {
				echo json_encode($response);
			}
		}
	}
	exit;
}

function invite() {
	global $userid;
	global $chatrooms_language;
	global $language;
	global $embed;
	global $embedcss;
	global $guestsMode;
	global $basedata;
	global $cookiePrefix;
	global $chromeReorderFix;
	global $hideOffline;
	global $plugins;
	global $firstguestID;
	$base_url = BASE_URL;

	$status['available'] = $language[30];
	$status['busy'] = $language[31];
	$status['offline'] = $language[32];
	$status['invisible'] = $language[33];
	$status['away'] = $language[34];

	if(!isset($_REQUEST['force'])){
		$force = 0;
	} else {
		$force = $_REQUEST['force'];
	}

	$id = sql_real_escape_string($_GET['roomid']);
	$inviteid = $_GET['inviteid'];
	$roomname = $_GET['roomname'];
	$popoutmode = 0;
	if(isset($_GET['popoutmode'])){
		$popoutmode = $_GET['popoutmode'];
	}

	$time = getTimeStamp();
	$invitedusers = array();
	$query = sql_query('getChatroomById',array('id'=>$id));

	if (defined('DEV_MODE') && DEV_MODE == '1') { echo sql_error($GLOBALS['dbh']); }

	$result = sql_fetch_assoc($query);
	$chatroomType = $result['type'];
	if($chatroomType == 2) {
		$invitedusers = array_filter(explode(',', $result['invitedusers']));
	}

	$query = sql_query('getchatroombannedusers',array('chatroomid'=>$id));

	if (defined('DEV_MODE') && DEV_MODE == '1') { echo sql_error($GLOBALS['dbh']); }

	$result = sql_fetch_assoc($query);
	$bannedUsers = explode(',',$result['bannedusers']);

	$onlineCacheKey = 'all_online';
	if($userid > $firstguestID){
		$onlineCacheKey .= 'guest';
	}
	$roleid = getRoleId($userid);
	if(!empty($roleid)){
		$onlineCacheKey .= $roleid;
	}
	if (!is_array($buddyList = getCache($onlineCacheKey))|| ($force == 1)) {
		$buddyList = array();
		$sql = getFriendsList($userid,$time);
		if($guestsMode){
			$sql = getGuestsList($userid,$time,$sql);
		}
		$query = sql_query($sql, array(), 1);

		if (defined('DEV_MODE') && DEV_MODE == '1') { echo sql_error($GLOBALS['dbh']); }

		while ($chat = sql_fetch_assoc($query)) {

			if (((($time-processTime($chat['lastactivity'])) < ONLINE_TIMEOUT) && $chat['status'] != 'invisible' && $chat['status'] != 'offline') || $chat['isdevice'] == 1) {
				if ($chat['status'] != 'busy' && $chat['status'] != 'away') {
					$chat['status'] = 'available';
				}
			} else {
				$chat['status'] = 'offline';
			}

			$avatar = getAvatar($chat['avatar']);

			if (!empty($chat['username'])) {
				if (function_exists('processName')) {
					$chat['username'] = processName($chat['username']);
				}

				if (!(in_array($chat['userid'],$bannedUsers)) && $chat['userid'] != $userid && ($hideOffline == 0||($hideOffline == 1 && $chat['status']!='offline'))) {
					$buddyList[$chromeReorderFix.$chat['userid']] = array('id' => $chat['userid'], 'n' => $chat['username'], 'a' => $avatar, 's' => $chat['status']);
				}
			}
		}
	}

	if (DISPLAY_ALL_USERS == 0 && MEMCACHE <> 0 && !checkAuthMode('social')) {
		$tempBuddyList = array();
		if (!is_array($friendIds = getCache('friend_ids_of_'.$userid))|| ($force == 1)) {
			$friendIds=array();
			$sql = getFriendsIds($userid);
			$query = sql_query($sql, array(), 1);
			if(sql_num_rows($query) == 1 ){
				$buddy = sql_fetch_assoc($query);
				$friendIds = explode(',',$buddy['friendid']);
			}else {
				while($buddy = sql_fetch_assoc($query)){
					$friendIds[]=$buddy['friendid'];
				}
			}
			setCache('friend_ids_of_'.$userid,$friendIds, 30);
		}
		foreach($friendIds as $friendId) {
			$friendId = $chromeReorderFix.$friendId;
			if (!empty($buddyList[$friendId])) {
				$tempBuddyList[$friendId] = $buddyList[$friendId];
			}
		}
		$buddyList = $tempBuddyList;
	}

	if (function_exists('hooks_forcefriends') && is_array(hooks_forcefriends())) {
		$buddyList = array_merge(hooks_forcefriends(),$buddyList);
	}

	$blockList = array();
	if (in_array('block',$plugins)) {
		$blockedIds = getBlockedUserIDs();
		foreach ($blockedIds as $bid) {
			array_push($blockList,$bid);
			if (isset($buddyList[$chromeReorderFix.$bid])) {
				unset($buddyList[$chromeReorderFix.$bid]);
			}
		}
	}

	$chatroomUserList = getChatroomUserIDs($id);
	foreach ($chatroomUserList as $cid) {
		if (isset($buddyList[$chromeReorderFix.$cid])) {
			unset($buddyList[$chromeReorderFix.$cid]);
		}
	}

	$s['available'] = '';
	$s['away'] = '';
	$s['busy'] = '';
	$s['offline'] = '';
	foreach ($buddyList as $buddy) {
		$invitedusers_class = '';
		$tooltip = '';
		if($buddy['id'] != $userid){
			if($chatroomType == 2 && count($invitedusers) > 0 && in_array($buddy['id'], $invitedusers)){
				$invitedusers_class = 'invitedusers';
				$tooltip = 'title="'.$chatrooms_language[73].'"';
			}
			$s[$buddy['s']] .= '<div class="invite_1"><div class="invite_2" '.$tooltip.' onclick="javascript:document.getElementById(\'check_'.$buddy['id'].'\').checked = document.getElementById(\'check_'.$buddy['id'].'\').checked?false:true;"><img class="useravatar" height=30 width=30 src="'.$buddy['a'].'" /></div><div class="invite_3" onclick="javascript:document.getElementById(\'check_'.$buddy['id'].'\').checked = document.getElementById(\'check_'.$buddy['id'].'\').checked?false:true;"><span class="invite_name">'.$buddy['n'].'</span><div class="cometchat_userscontentdot cometchat_margin_top cometchat_user_'.$buddy['s'].'"></div><span class="invite_5">'.$status[$buddy['s']].'</span></div><label class="cometchat_checkboxcontrol cometchat_checkboxouter"><input class="cometchat_checkbox" type="checkbox" name="invite[]" value="'.$buddy['id'].'" id="check_'.$buddy['id'].'" class="invite_4" /><div class="cometchat_controlindicator"></div></label></div>';
		}
	}

	$inviteContent = '';
	$invitehide = '';
	$inviteContent = $s['available']."".$s['away']."".$s['offline'];
	if(empty($inviteContent)) {
		$inviteContent = '<div class="lobby_noroom">'.$chatrooms_language['no_users_available'].'</div>';
		$invitehide = 'style="display:none;"';
	}
	generateUserlistForm('inviteusers',$inviteContent,$chatrooms_language['invite_users_title'],$chatrooms_language['invite_users_button'],$id,$inviteid,$roomname);
	exit;
}

function inviteusers() {
	global $chatrooms_language;
	global $close;
	global $embedcss;
	$post_roomid = sql_real_escape_string($_REQUEST['roomid']);
	$post_inviteid = sql_real_escape_string($_REQUEST['inviteid']);
	$post_roomname = sql_real_escape_string($_REQUEST['roomname']);
	$base_url = BASE_URL;

	if(!empty($_REQUEST['invite'])){
		$blockedIds = getBlockedUserIDs();
		foreach ($blockedIds as $bid) {
			if(in_array($bid, $_REQUEST['invite'])){
				unset($_REQUEST['invite'][array_search($bid, $_REQUEST['invite'])]);
			}
		}
		foreach ($_REQUEST['invite'] as $user) {
			$chatroomroomname = base64_decode(rawurldecode($_REQUEST['roomname']));
			$invitemessage = $chatrooms_language[18].$chatroomroomname.' <a href="javascript:jqcc.cometchat.joinChatroom(\''.$post_roomid.'\',\''.$post_inviteid.'\',\''.$post_roomname.'\')">'.$chatrooms_language[19].'</a>';
			$response = sendMessage($user,$invitemessage,1);
			addUsersToChatroom($post_roomid, $user);
			$processedMessage = $_SESSION['cometchat']['user']['n'].": "."has invited you to join ".$chatroomroomname;
			if($response != ''){
				pushMobileNotification($user,$response['id'],$processedMessage);
			}
		}
	}
	showSuccessfulInvitation($post_roomid,$chatrooms_language[18],$chatrooms_language[16]);
	exit;
}

function showSuccessfulInvitation($roomid,$title,$successtext){
	global $close;
	global $embedcss;
	$base_url = BASE_URL;
	echo <<<EOD
<!DOCTYPE html>
<html>
	<head>
		<title>{$title}</title>
		<meta name="viewport" content="user-scalable=0,width=device-width, minimum-scale=1.0, maximum-scale=1.0, initial-scale=1.0" />
		<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
		<link type="text/css" rel="stylesheet" media="all" href="{$base_url}css.php?type=module&name=chatrooms" />
		<script type="text/javascript">
			function closePopup(name){
				var controlparameters = {'type':'modules', 'name':'chatrooms', 'method':'closeCCPopup', 'params':{'name':'invite','roomid':'{$roomid}'}};
				controlparameters = JSON.stringify(controlparameters);
				if(typeof(parent) != 'undefined' && parent != null && parent != self){
					parent.postMessage('CC^CONTROL_'+controlparameters,'*');
				} else {
					window.close();
				}
			}
		</script>
		<style>
		body{
			margin: 0px;
		}
		</style>
	</head>
	<body onload="{$close}">
		<div class="cometchat_wrapper">
			<div class="container_body container_body_layout {$embedcss}">
				{$successtext}
				<div style="clear:both"></div>
			</div>
		</div>
	</body>
</html>
EOD;
}

function passwordBox() {

	global $chatrooms_language;
	global $embedcss;
	$base_url = BASE_URL;

	$close = 'setTimeout("window.close()",1000);';
	if (!empty($_GET['embed']) && $_GET['embed'] == 'web') {
		$embed = 'web';
		$embedcss = 'embed';
		$close = 'setTimeout("closePopup();",1000);';
	}

	$id = $_REQUEST['id'];
	$name = $_REQUEST['name'];
	$silent = $_REQUEST['silent'];
	$cc_theme = '';
	$noBar = 0;
	if(!empty($_GET['cc_theme'])){
		$cc_theme = '&cc_theme='.$_GET['cc_theme'];
	}
	if(!empty($_GET['noBar'])){
		$noBar = $_GET['noBar'];
	}

	$options=" <input type=button id='passwordBox' class='invitebutton' value='$chatrooms_language[19]' /><input type=button id='close' class='invitebutton' onclick=$close value='$chatrooms_language[51]' />";

echo <<<EOD
<!DOCTYPE html>
<html>
	<head>
		<title>{$name}</title>
		<meta name="viewport" content="user-scalable=0,width=device-width, minimum-scale=1.0, maximum-scale=1.0, initial-scale=1.0" />
		<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
		<link type="text/css" rel="stylesheet" media="all" href="{$base_url}css.php?type=module&name=chatrooms{$cc_theme}" />
		<script src="{$base_url}js.php?type=core&name=jquery"></script>
		<script>
			$ = jQuery = jqcc;
		</script>
		<script type="text/javascript">
		function closePopup(name){
			var controlparameters = {'type':'modules', 'name':'chatrooms', 'method':'closeCCPopup', 'params':{'name':'passwordBox'}};
			controlparameters = JSON.stringify(controlparameters);
			if(typeof(parent) != 'undefined' && parent != null && parent != self){
				parent.postMessage('CC^CONTROL_'+controlparameters,'*');
			} else {
				window.close();
			}
		}
		function cccheckPass() {
			password = $('#chatroomPass').val();
			var controlparameters = {"type":"modules", "name":"cometchat", "method":"checkChatroomPass", "params":{"id":"{$id}", "name":"{$name}", "silent":"{$silent}", "password":password, "clicked":1, "encryptPass":1, "noBar":"{$noBar}"}};
			controlparameters = JSON.stringify(controlparameters);
			if(window.opener==null || window.opener==''){
				parent.postMessage('CC^CONTROL_'+controlparameters,'*');
			}else{
				window.opener.postMessage('CC^CONTROL_'+controlparameters,'*');
			}
		}
		$(function() {
			var controlparameters = {'type':'module', 'name':'chatrooms', 'method':'resizeCCPopup', 'params':{"id":"passwordBox", "width":110, "height":320}};
			controlparameters = JSON.stringify(controlparameters);
			if(typeof(window.opener) == null){
				window.opener.postMessage('CC^CONTROL_'+controlparameters,'*');
			}else{
				parent.postMessage('CC^CONTROL_'+controlparameters,'*');
			}
			$('#passwordBox').click(function(e) {
				cccheckPass();
				{$close}
			});

			$('#chatroomPass').keyup(function(e) {
				if(e.keyCode == 13) {
					cccheckPass();
					{$close}
				}
			});
		});
		</script>
	</head>
	<body>
		<div class="container passwordBox_container">
			<div class="container_title {$embedcss}">{$name}</div>
			<div style="overflow:hidden;" class="container_body {$embedcss}">
			<div class="passwordbox_body">{$chatrooms_language[8]}</div>
			<input style="width: 95%;margin-top: 8px;" id="chatroomPass" type="password" name="pwd" autofocus/>
				<div style="clear:both"></div>
			</div>
			<div align="right" class="cometchat_container_sub {$embedcss}">{$options}</div>
		</div>
	</body>
</html>
EOD;

exit;
}

function loadChatroomPro() {

	global $chatrooms_language;
	global $language;
	global $embed;
	global $embedcss;
	global $userid;
	global $moderatorUserIDs;
	global $lightboxWindows;
	global $showchatbutton;
	global $chromeReorderFix;
	global $firstguestID;
	$base_url = BASE_URL;

	$close = 'setTimeout("window.close()",2000);';
	$callbackfn='';
	if(!empty($_REQUEST['callbackfn'])){
		$callbackfn=$_REQUEST['callbackfn'];
	}
	if (!empty($_GET['embed']) && $_GET['embed'] == 'web') {
		$embed = 'web';
		$embedcss = 'embed';
		$close = 'parent.closeCCPopup("loadChatroomPro");';
	}
	$callerWindow = '';
	if(!empty($_REQUEST['caller'])){
		$callerWindow = $_REQUEST['caller'];
	}

	$id = sql_real_escape_string($_GET['roomid']);
	$cc_theme = '';
	if(!empty($_GET['cc_theme'])){
		$cc_theme = '&cc_theme='.$_GET['cc_theme'];
	}
	$uid = sql_real_escape_string($_GET['inviteid']);
	$owner = $_GET['owner'];
	$apiAccess = 0;
	if(!empty($_GET['apiAccess']) && $_GET['apiAccess'] != 'undefined'){
		$apiAccess = $_GET['apiAccess'];
	}

	$options = "";
	$status_area = "";
	$popoutmode = $_GET['popoutmode'];

	$onlineCacheKey = 'all_online';
	if($userid > $firstguestID){
		$onlineCacheKey .= 'guest';
	}
	$roleid = getRoleId($userid);
	if(!empty($roleid)){
		$onlineCacheKey .= $roleid;
	}
	if (!is_array($buddyList = getCache($onlineCacheKey))) {
		$buddyList = array();
		$sql = getFriendsList($userid,$time);
		if($guestsMode){
			$sql = getGuestsList($userid,$time,$sql);
		}
		$query = sql_query($sql, array(), 1);

		if (defined('DEV_MODE') && DEV_MODE == '1') { echo sql_error($GLOBALS['dbh']); }

		while ($chat = sql_fetch_assoc($query)) {

			if (((($time-processTime($chat['lastactivity'])) < ONLINE_TIMEOUT) && $chat['status'] != 'invisible' && $chat['status'] != 'offline') || $chat['isdevice'] == 1) {
				if ($chat['status'] != 'busy' && $chat['status'] != 'away') {
					$chat['status'] = 'available';
				}
			} else {
				$chat['status'] = 'offline';
			}

			$avatar = getAvatar($chat['avatar']);

			if (!empty($chat['username'])) {
				if (function_exists('processName')) {
					$chat['username'] = processName($chat['username']);
				}

				if (!(in_array($chat['userid'],$bannedUsers)) && $chat['userid'] != $userid && ($hideOffline == 0||($hideOffline == 1 && $chat['status']!='offline'))) {
					$buddyList[$chromeReorderFix.$chat['userid']] = array('id' => $chat['userid'], 'n' => $chat['username'], 'a' => $avatar, 's' => $chat['status']);
				}
			}
		}
	}
	if (DISPLAY_ALL_USERS == 0 && MEMCACHE <> 0 && !checkAuthMode('social')) {
		$tempBuddyList = array();
		if (!is_array($friendIds = getCache('friend_ids_of_'.$userid))|| ($force == 1)) {
			$friendIds=array();
			$sql = getFriendsIds($userid);
			$query = sql_query($sql, array(), 1);
			if(sql_num_rows($query) == 1 ){
				$buddy = sql_fetch_assoc($query);
				$friendIds = explode(',',$buddy['friendid']);
			}else {
				while($buddy = sql_fetch_assoc($query)){
					$friendIds[]=$buddy['friendid'];
				}
			}
			setCache('friend_ids_of_'.$userid,$friendIds, 30);
		}
		foreach($friendIds as $friendId) {
			$friendId = $chromeReorderFix.$friendId;
			if (!empty($buddyList[$friendId])) {
				$tempBuddyList[$friendId] = $buddyList[$friendId];
			}
		}
		$buddyList = $tempBuddyList;
	}

	if (function_exists('hooks_forcefriends') && is_array(hooks_forcefriends())) {
		$buddyList = array_merge(hooks_forcefriends(),$buddyList);
	}

	if($apiAccess && ($showchatbutton == '0' || ($showchatbutton == '1' && array_key_exists($chromeReorderFix.$uid, $buddyList)))){
		$options="<input type=button class='invitebutton chat' caller='".$callerWindow."' uid=".$uid." value='".$chatrooms_language[43]."' />";
	}

	if($owner == 1 || in_array($userid,$moderatorUserIDs)) {
		$query = sql_query('getChatroomById',array('id'=>$id));
		$room = sql_fetch_assoc($query);

		if(!in_array($uid,$moderatorUserIDs) && $uid != $room['createdby']) {
			$options = "<input type=button id='cc_kick' value='".$chatrooms_language[40]."' uid = ".$uid." class='invitebutton kickBan'/>
			<input type=button id='cc_ban' value='".$chatrooms_language[41]."' uid = ".$uid." class='invitebutton kickBan' />".$options;
		}
	}

	if (defined('DEV_MODE') && DEV_MODE == '1') { echo sql_error($GLOBALS['dbh']); }

	$sql = getUserDetails($uid);

	if($uid>$firstguestID) {
		$sql = getGuestDetails($uid);
	}

	$res = sql_query($sql, array(), 1);
	$result = sql_fetch_assoc($res);
	$link = fetchLink($result['link']);
	$avatar = getAvatar($result['avatar']);
	$status = $result['status'];
	$statusMessage = $result['message'];
	$isDevice = $result['isdevice'];
	$icon = '';

	if($statusMessage==null && $status == 'available'){
		$statusMessage = $language[30];
	} else if($statusMessage==null && $status == 'away'){
		$statusMessage = $language[34];
	}else if($statusMessage==null && $status == 'busy'){
		$statusMessage = $language[31];
	}else if($statusMessage==null && $status == 'offline'){
		$statusMessage = $language[32];
	}else if($statusMessage==null && $status == 'invisible'){
		$statusMessage = $language[33];
	}

	$usercontentstatus = $status;
	if($isDevice==1){
		$usercontentstatus = 'mobile cometchat_mobile_'.$status;
		$icon = '<div class="cometchat_dot"></div>';
	}

	$status_area = '<span class="cometchat_userscontentdot cometchat_userscontentdot_synergy cometchat_'.$usercontentstatus.' cometchat_'.$usercontentstatus.'_synergy">'.$icon.'</span><span class="status_messagearea">'.$statusMessage.'</span>';

	if($link != '' && $uid < $firstguestID && $callbackfn!='desktop') {
		$options .= " <input type=button class='invitebutton' onClick=javascript:window.open('".$link."');".$close." value='".$chatrooms_language[42]."' />";
	}

echo <<<EOD
<!DOCTYPE html>
<html>
	<head>
		<title>{$result['username']}</title>
		<meta name="viewport" content="user-scalable=0,width=device-width, minimum-scale=1.0, maximum-scale=1.0, initial-scale=1.0" />
		<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
		<script src="{$base_url}js.php?type=core&name=jquery"></script>
		<script>
			$ = jQuery = jqcc;
		</script>
		<link type="text/css" rel="stylesheet" media="all" href="{$base_url}css.php?type=module&name=chatrooms{$cc_theme}" />
		<script>
		$('.kickBan').live('click',function(){
			var uid = $(this).attr('uid');
			var method = $(this).attr('id');
			if(method == 'cc_kick'){
				action = 'kickChatroomUser';
			} else {
				action = 'banChatroomUser';
			}
			var controlparameters = {"type":"modules", "name":"cometchat", "method":action, "params":{"uid":uid, "allowed":"1", "chatroommode":"1"}};
			controlparameters = JSON.stringify(controlparameters);
			if(typeof(parent) != 'undefined' && parent != null && parent != self){
				parent.postMessage('CC^CONTROL_'+controlparameters,'*');
				var controlparameters = {'type':'plugins', 'name':'chatrooms', 'method':'closeCCPopup', 'params':{'name':'loadChatroomPro'}};
				controlparameters = JSON.stringify(controlparameters);
				parent.postMessage('CC^CONTROL_'+controlparameters,'*');
			} else {
				window.opener.postMessage('CC^CONTROL_'+controlparameters,'*');
				window.close();
			}

		});

		$('.chat').live('click',function(){
			var uid = $(this).attr('uid');
			var caller = $(this).attr('caller');
			var callbackfn="<?php echo $callbackfn; ?>";
			var controlparameters = {"type":"modules", "name":"cometchat", "method":"chatWith", "params":{"uid":uid, "chatroommode":"0", "caller":caller}};
			if(callbackfn){
				controlparameters = {"type":"modules", "name":"cometchat", "method":"chatWith", "params":{"uid":uid, "chatroommode":"0"}};
			}
			controlparameters = JSON.stringify(controlparameters);
			if(typeof(parent) != 'undefined' && parent != null && parent != self){
				parent.postMessage('CC^CONTROL_'+controlparameters,'*');
				var controlparameters = {'type':'plugins', 'name':'chatrooms', 'method':'closeCCPopup', 'params':{'name':'loadChatroomPro'}};
				controlparameters = JSON.stringify(controlparameters);
				parent.postMessage('CC^CONTROL_'+controlparameters,'*');
			} else {
				window.opener.postMessage('CC^CONTROL_'+controlparameters,'*');
				window.close();
			}
		});
		</script>
	</head>
	<body style="margin:0;">
		<form method="post">
			<div class="cometchat_wrapper">
				<div class="container_title {$embedcss}">{$result['username']}</div>
				<div class="container_body {$embedcss}" style='height:auto'>
					<div class="chatroom_avatar"><img src="{$avatar}" height="50px" width="50px" /></div>
					<div class="status_container">
						<div class="status_area">{$status_area}</div>
						<div class="control_buttons">{$options}</div>
					</div>
					<div style='clear:both'></div>
				</div>
			</div>
		</form>
		<script type='text/javascript'>
			if(typeof $ != 'undefined')
			$(document).ready(function(){
				sum = 0;
				$('.control_buttons input').each(function(i,o){
					sum += $(o).outerWidth(false);
				});
				setTimeout(function(){
					window.resizeTo(sum+140, ($('form').outerHeight(false)+window.outerHeight-window.innerHeight+10));
					//140 = container.padding(10*2) + avatar(50) + buttons.margin-left(20) + buttons.margin-right(20) + 30 (container margin(2%)+ inter-button spacing(taking worst case scenario))
					//10 = container.margin(5*2)
				},500);
				var mobileDevice = navigator.userAgent.match(/ipad|ipod|iphone|android|windows ce|Windows Phone|blackberry|palm|symbian/i);
				if(typeof(parent) != 'undefined' && !mobileDevice){
					var controlparameters = {'type':'module', 'name':'chatrooms', 'method':'resizeCCPopup', 'params':{"id":"loadChatroomPro", "height":sum+178, "width":96}};
					controlparameters = JSON.stringify(controlparameters);
					if(typeof(window.opener) == null){
						window.opener.postMessage('CC^CONTROL_'+controlparameters,'*');
					}else{
						parent.postMessage('CC^CONTROL_'+controlparameters,'*');
					}
				}
				//Height 80 = container_body.height(50) + embed.padding(10*2) + container.margin(5*2)
			});
		</script>
	</body>
</html>
EOD;

exit;
}

function leavechatroom() {
	global $userid;
	global $cookiePrefix;
	if (empty($_REQUEST['banflag'])) {
		$query = sql_query('leavechatroom',array('userid'=>$userid, 'chatroomid'=>$_REQUEST['currentroom']));
	}
	$joinedChatroomIds = $_SESSION['cometchat']['cometchat_joinedchatroomids'];
	$key = array_search($_REQUEST['currentroom'],$joinedChatroomIds);
	if($key!==false){
		unset($joinedChatroomIds[$key]);
	}
	$_SESSION['cometchat']['cometchat_joinedchatroomids'] = $joinedChatroomIds;

	removeCache('chatrooms_users'.$_REQUEST['currentroom']);
	removeCache('chatroom_list');

	unset($_SESSION['cometchat']['cometchat_chatroom_'.$_REQUEST['currentroom']]);
	unset($_SESSION['cometchat']['cometchat_chatroomslist']);
	unset($_SESSION['cometchat']['isModerator']);
	header('Content-type: application/json; charset=utf-8');
	if (!empty($_GET['callback'])) {
		echo $_GET['callback'].'('.json_encode($_REQUEST['currentroom']).')';
	} else {
		echo json_encode($_REQUEST['currentroom']);
	}
	exit;
}

function kickUser() {
	global $cookiePrefix;
	$kickid = $_REQUEST['kickid'];
	$id = $_REQUEST['currentroom'];
	if (empty($_REQUEST['kick']) && empty($_SESSION['cometchat']['isModerator']) ) {
		echo 0;
		exit;
	}

	$query = sql_query('kickUser',array('userid'=>$kickid, 'chatroomid'=>$id));
	$controlparameters = array('type' => 'modules', 'name' => 'chatroom', 'method' => 'kicked', 'params' => array('id' => $kickid));
	$controlparameters = json_encode($controlparameters);
	sendChatroomMessage($id,'CC^CONTROL_'.$controlparameters,0);
	removeCache('chatrooms_users'.$id);
	removeCache('chatroom_list');
	echo 1;
	exit;
}

function banUser() {
	global $cookiePrefix;
	$banid = $_REQUEST['banid'];
	$id = $_REQUEST['currentroom'];
	$popoutmode	= $_REQUEST['popoutmode'];
	if (empty($_REQUEST['ban']) && empty($_SESSION['cometchat']['isModerator']) ) {
		echo 0;
		exit;
	}

	$query =sql_query('banUser',array('userid'=>$banid, 'chatroomid'=>$id));

	addUsersToChatroom($id, $banid, 1);

	$controlparameters = array('type' => 'modules', 'name' => 'chatroom', 'method' => 'banned', 'params' => array('id' => $banid));
	$controlparameters = json_encode($controlparameters);
	sendChatroomMessage($id,'CC^CONTROL_'.$controlparameters,0);
	removeCache('chatrooms_users'.$id);
	removeCache('chatroom_list');
	echo 1;
	exit;
}

function unban() {
	global $userid;
	global $chatrooms_language;
	global $language;
	global $embed;
	global $embedcss;
	global $guestsMode;
	global $basedata;
	global $chromeReorderFix;
	$base_url = BASE_URL;

	$status['available'] = $language[30];
	$status['busy']		 = $language[31];
	$status['offline']	 = $language[32];
	$status['invisible'] = $language[33];
	$status['away']		 = $language[34];

	if(!empty($_REQUEST['callbackfn']) && $_REQUEST['callbackfn'] == 'mobileapp'){
		$id = sql_real_escape_string($_REQUEST['roomid']);
	}else{
		$id 		= sql_real_escape_string($_GET['roomid']);
		$inviteid 	= sql_real_escape_string($_GET['inviteid']);
		$roomname 	= sql_real_escape_string($_GET['roomname']);
	}

	$cc_theme = '';
	if(!empty($_GET['cc_theme'])){
		$cc_theme = '&cc_theme='.$_GET['cc_theme'];
	}
	$time = getTimeStamp();
	$buddyList = array();
	$sql = sql_getQuery('unban',array('userid'=>$userid, 'chatroomid'=>$id));

	if ($guestsMode) {
		$sql = getChatroomBannedGuests($id,$time,$sql);
	}

	$query = sql_query($sql, array(), 1);

	if (defined('DEV_MODE') && DEV_MODE == '1') { echo sql_error($GLOBALS['dbh']); }

	while ($chat = sql_fetch_assoc($query)) {

		$avatar = getAvatar($chat['avatar']);

		if (!empty($chat['username'])) {
			if (function_exists('processName')) {
				$chat['username'] = processName($chat['username']);
			}

			$buddyList[$chromeReorderFix.$chat['userid']] = array('id' => $chat['userid'], 'n' => $chat['username'], 'a' => $avatar,'s' => $chat['status']);
		}
	}
	if(!empty($_REQUEST['callbackfn']) && $_REQUEST['callbackfn'] == 'mobileapp'){
		$response['unban'] = $buddyList;
		echo json_encode($response);
		exit;
	}

	$s['count'] = '';

	foreach ($buddyList as $buddy) {
		$s['count'] .= '<div class="invite_1"><div class="invite_2" onclick="javascript:document.getElementById(\'check_'.$buddy['id'].'\').checked = document.getElementById(\'check_'.$buddy['id'].'\').checked?false:true;"><img class="useravatar" height=30 width=30 src="'.$buddy['a'].'" /></div><div class="invite_3" onclick="javascript:document.getElementById(\'check_'.$buddy['id'].'\').checked = document.getElementById(\'check_'.$buddy['id'].'\').checked?false:true;"><span class="invite_name">'.$buddy['n'].'</span><div style="margin: 4px 6px 0px 0px;" class="cometchat_userscontentdot cometchat_user_'.$buddy['s'].'"></div><div class="cometchat_buddylist_status">'.$buddy['s'].'</div></div><label class="cometchat_checkboxcontrol cometchat_checkboxouter"><input class="cometchat_checkbox" type="checkbox" name="unban[]" value="'.$buddy['id'].'" id="check_'.$buddy['id'].'" class="invite_4" /><div class="cometchat_controlindicator"></div></label></div>';

	}

	if($s['count'] == ''){
		$s['count'] = '<div class="lobby_noroom">'.$chatrooms_language['no_users_to_unban'].'</div>';
	}
	generateUserlistForm('unbanusers',$s['count'],$chatrooms_language['select_users'],$chatrooms_language['unban_users'],$id,$inviteid,$roomname);
	exit;
}
function generateUserlistForm($action,$userlist,$title,$submittext,$id,$inviteid,$roomname){
	global $embedcss,$embed,$basedata;
	$cc_theme = '';
	if(!empty($_GET['cc_theme'])){
		$cc_theme = '&cc_theme='.$_GET['cc_theme'];
	}
	$base_url = BASE_URL;
		echo <<<EOD
<!DOCTYPE html>
<html>
	<head>
		<title>{$title}</title>
		<meta name="viewport" content="user-scalable=0,width=device-width, height=device-height minimum-scale=1.0, maximum-scale=1.0, initial-scale=1.0" />
		<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
		<link type="text/css" rel="stylesheet" media="all" href="{$base_url}css.php?type=module&name=chatrooms{$cc_theme}" />
		<script src="{$base_url}js.php?type=core&name=jquery"></script>
		<script src="{$base_url}js.php?type=core&name=scroll"></script>
		<style>
			body{
				margin:	0px;
			}
		</style>
	</head>
	<body>
		<form method="post" action="{$base_url}modules/chatrooms/chatrooms.php?action={$action}&embed={$embed}&basedata={$basedata}">
			<div class="cometchat_wrapper">
				<div class="container_body container_body_layout1 {$embedcss}">
					{$userlist}
					<div style="clear:both"></div>
				</div>
				<div class="cometchat_container_sub container_subbox {$embedcss}">
					<input type=submit value="{$submittext}" class="{$action}" disabled />
				</div>
				<script>
					jqcc(document).ready(function(){
						jqcc('body').css('overflow','hidden');
						var mobileDevice = navigator.userAgent.match(/ipad|ipod|iphone|android|windows ce|Windows Phone|blackberry|palm|symbian/i);
						if(mobileDevice){
							jqcc('.container_body').css('height',jqcc(window).height()-(jqcc('.container_title').outerHeight(true)+jqcc('.cometchat_container_sub').outerHeight(true)+30));
							jqcc('body').css({'overflow':'hidden','overflow-y':'auto'});
						} else {
							var contentheight = (window.innerHeight - jqcc('.cometchat_container_sub').outerHeight())+'px';
							if(jqcc().slimScroll){
								jqcc('.container_body ').css({'height': contentheight});
								jqcc('.container_body ').slimScroll({scroll: 0,height: contentheight});
							}
						}
						jqcc('.invite_1').click(function(){
							var checked = jqcc( "input:checked" ).length;
							if(checked > 0){
								jqcc('.{$action}').attr("disabled", false);
							}else{
								jqcc('.{$action}').attr("disabled", true);
							}
						});
						if(jqcc(".invite_1").length == 0){

							jqcc('.container_body').css({'height':window.innerHeight-16+'px'});
							if(jqcc('.container_body').parent().hasClass('slimScrollDiv')){
								jqcc('.container_body').parent().height(window.innerHeight-16+'px');
							}
							jqcc('.container_subbox').hide();
						}
					});
				</script>
			</div>
			<input type="hidden" name="roomid" value="{$id}" />
			<input type="hidden" name="inviteid" value="{$inviteid}" />
			<input type="hidden" name="roomname" value="{$roomname}" />
		</form>
	</body>
</html>
EOD;
}
function unbanusers() {

	global $chatrooms_language;
	global $close;
	global $embedcss;
	$base_url = BASE_URL;

	if (empty($_SESSION['cometchat']['isModerator']) ) {
		echo 0;
		exit;
	}

	if (!empty($_REQUEST['callbackfn']) && $_REQUEST['callbackfn'] == 'mobileapp'){
		$post_roomid = $_REQUEST['roomid'];
		$post_inviteid = $_REQUEST['inviteid'];
		$post_roomname = $_REQUEST['roomname'];
		$temp = $_REQUEST['unban'];

		$temp2 = substr($temp, 1);
		$temp2 = substr($temp2, 0, strlen($temp2)-1);

		$users = explode(",",$temp2);

		if (!empty($_REQUEST['unban'])) {
			foreach($users as $user) {
				$query = sql_query('kickUser',array('userid'=>$user, 'chatroomid'=>$post_roomid));
				$query = sql_query('deleteBanUserMessage',array('chatroomid'=>$post_roomid, 'userid'=>$user));
				$chatroomroomname = $post_roomname;
				sendMessage($user,"{$chatrooms_language[18]}{$chatroomroomname}. <a href=\"javascript:jqcc.cometchat.joinChatroom('{$post_roomid}','{$post_inviteid}','{$post_roomname}')\">{$chatrooms_language[19]}</a>",1);
			}
			echo 1;
			exit;
		}
	}else{
		if(!empty($_REQUEST['unban'])){
			foreach ($_REQUEST['unban'] as $user) {
				$query = sql_query('unbanusers',array('chatroomid'=>$_REQUEST['roomid'], 'userid'=>$user));
				$query = sql_query('deleteBanUserMessage',array('chatroomid'=>$_REQUEST['roomid'], 'userid'=>$user));
				$post_roomid = sql_real_escape_string($_REQUEST['roomid']);
				$post_inviteid = sql_real_escape_string($_REQUEST['inviteid']);
				$post_roomname = sql_real_escape_string($_REQUEST['roomname']);
				$chatroomroomname = base64_decode(rawurldecode($post_roomname));
				addUsersToChatroom($post_roomid, $user);
				sendMessage($user,"{$chatrooms_language[18]}{$chatroomroomname}. <a href=\"javascript:jqcc.cometchat.joinChatroom('{$post_roomid}','{$post_inviteid}','{$post_roomname}')\">{$chatrooms_language[19]}</a>",1);
			}
		}
	}
	showSuccessfulInvitation($post_roomid,$chatrooms_language[18],$chatrooms_language[16]);
	exit;
}

function deleteChatroomMessage() {
	$id = $_REQUEST['currentroom'];
	$delid = $_REQUEST['delid'];
	global $allowdelete;
	global $userid;
	$deleteflag = 0;

	if (!empty($_SESSION['cometchat']['isModerator'])) {
		$deleteflag = 1;
	} elseif (empty($allowdelete)){
		$query = sql_query('getUserIdByChatroom',array('id'=>$delid));
		$row = sql_fetch_assoc($query);
		if ($row['userid'] == $userid) {
			$deleteflag = 1;
		}
	}
	if (empty($deleteflag)) {
		echo 0;
		exit;
	} else {
		sendCCResponse(1);
	}

	$del = $delid;
	$query = sql_query('deleteChatroomMessage',array('id'=>$del, 'chatroomid'=>$id));
	$controlparameters = array('type' => 'modules', 'name' => 'chatroom', 'method' => 'deletemessage', 'params' => array('id' => $delid));
	$controlparameters = json_encode($controlparameters);
	sendChatroomMessage($id,'CC^CONTROL_'.$controlparameters,0);
	exit;
}

function addUsersToChatroom($roomid, $inviteid, $remove = 0) {

	if(empty($roomid) || empty($inviteid)) { return; }

	$query = sql_query('getChatroomById',array('id'=>$roomid));
	if (defined('DEV_MODE') && DEV_MODE == '1') { echo sql_error($GLOBALS['dbh']); }
	$row = sql_fetch_assoc($query);

	if($row['type'] != 2) { return; }

	$userList = array();
	$implodedUserList = '';
	$updateInvitedUsers = 0;

	if(!($invitedusers = $row['invitedusers'])) {
		$invitedusers = '';
	}
	$userList = array_filter(explode(',', $invitedusers));
	if(!in_array($inviteid, $userList) && $remove == 0){
		$userList[] = $inviteid;
		$updateInvitedUsers = 1;
	}
	if($remove == 1){
		$key = array_search($inviteid,$userList);
		if($key!==false){
			unset($userList[$key]);
			$updateInvitedUsers = 1;
		}
	}
	if($updateInvitedUsers == 1){
		$implodedUserList = implode(',',$userList);
		$query = sql_query('addUsersToChatroom',array('id'=>$roomid, 'invitedusers'=>$implodedUserList));
		if (defined('DEV_MODE') && DEV_MODE == '1') { echo sql_error($GLOBALS['dbh']); }
	}
}

function getChatroomUserIDs($chatroomid) {
	$chatroomusers = array();
	$query = sql_query('getChatroomUserIDs',array('chatroomid'=>$chatroomid));

	if (defined('DEV_MODE') && DEV_MODE == '1') { echo sql_error($GLOBALS['dbh']); }

	while ($result = sql_fetch_assoc($query)) {
		array_push($chatroomusers, $result['chatroomusers']);
	}
	return $chatroomusers;
}

$allowedActions = array('sendChatroomMessage','heartbeat','createchatroom','deletechatroom','checkpassword','invite','inviteusers','unban','unbanusers','passwordBox','loadChatroomPro','leavechatroom','kickUser','banUser','deleteChatroomMessage','getChatroomDetails','renamechatroom','getchatroomusers','getChatroomName');

if (!empty($_REQUEST['action']) && in_array($_REQUEST['action'],$allowedActions)) {
	if($_REQUEST['action']=='getChatroomDetails' && !empty($_REQUEST['id'])){
		call_user_func($_REQUEST['action'],$_REQUEST['id']);
	}else{
		call_user_func($_REQUEST['action']);
	}
}
