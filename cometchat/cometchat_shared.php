<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/
set_error_handler('error_handler',E_ALL|E_STRICT);

global $dbh,$userid,$memcache,$pushplatformsuffix;

$_REQUEST = array_merge($_GET, $_POST);

function defineStatusConstants(){
	define('COMETCHAT_STATUS_FIELDS', "cometchat_status.lastactivity , cometchat_status.lastseen, cometchat_status.lastseensetting , cometchat_status.status,  cometchat_status.isdevice, cometchat_status.readreceiptsetting ");
	define('COMETCHAT_STATUS_TABLE', "left join cometchat_status on ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." = cometchat_status.userid");
	define('COMETCHAT_STATUS_OFFLINE_CONDITION', "where ((cometchat_status.lastactivity > (".sql_real_escape_string($time)."-".((ONLINE_TIMEOUT)*2).")) OR cometchat_status.isdevice = 1) and (cometchat_status.status IS NULL OR cometchat_status.status <> 'invisible' OR cometchat_status.status <> 'offline')");
}

if(!empty($_REQUEST['appinfo'])){
	if(gettype($_REQUEST['appinfo']) == 'string'){
		$_REQUEST['appinfo'] = json_decode($_REQUEST['appinfo'],true);
	}
	if(!empty($_REQUEST['appinfo']['os']) && !empty($_REQUEST['appinfo']['os']['n'])){
		$pushplatformsuffix = $_REQUEST['appinfo']['os']['n'];
	}
}

if(!function_exists("mysqli_connect")){
	function mysqli_connect($db_server,$db_username,$db_password,$db_name,$port){
		return mysql_connect($db_server.':'.$port,$db_username,$db_password);
	}

	function mysqli_real_escape_string($dbh,$userid){
		return mysql_real_escape_string($userid);
	}

	function mysqli_select_db($dbh,$db_name){
		return mysql_select_db($db_name,$dbh);
	}

	function mysqli_connect_errno($dbh){
		return !$dbh;
	}

	function mysqli_query($dbh,$sql){
		return mysql_query($sql);
	}

	function mysqli_multi_query($dbh,$sql){
		global $dbms;
		if($dbms == "mssql"){
			$sqlarr = explode(';', $sql);
			foreach ($sqlarr as $sql){
				$result  = sqlsrv_query($dbh, $sql);
			}
			return $result;
		}else{
			return mysqli_multi_query($dbh,$sql);
		}
	}

	function mysqli_error($dbh){
		return mysql_error();
	}

	function mysqli_fetch_assoc($query){
		return mysql_fetch_assoc($query);
	}

	function mysqli_insert_id($dbh){
		return mysql_insert_id();
	}

	function mysqli_num_rows($query){
		return mysql_num_rows($query);
	}

	function mysqli_affected_rows($query){
		return mysql_affected_rows($query);
	}
}

function sql_connect(){
	/*This function is used for database connectivity*/
	global $dbms;
	global $dbh;

	$port = DB_PORT;
	if(empty($port)){
		$port = '3306';
	}

	$dbserver = explode(':',DB_SERVER);

	if(!empty($dbserver[1])){
	    $port = $dbserver[1];
	}

	$db_server = $dbserver[0];

	if($dbms == 'mssql'){
		$connectionInfo = array( "Database"=>DB_NAME, "UID"=>DB_USERNAME, "PWD"=>DB_PASSWORD , "CharacterSet" => "UTF-8");
		$dbh = sqlsrv_connect($db_server,$connectionInfo);
	}elseif($dbms == 'pgsql'){
		if(empty($port)){
			$port = '5432';
		}
	    $dbh = pg_connect("host= ".$db_server." port=".$port." dbname=".DB_NAME." user=".DB_USERNAME." password=".DB_PASSWORD);

	}else{
		$dbh = mysqli_connect($db_server,DB_USERNAME,DB_PASSWORD,DB_NAME,$port);

		if (sql_connect_errno($dbh)) {
			$dbh = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_NAME,$port,'/tmp/mysql5.sock');
		}
		if (sql_connect_errno($dbh)) {
			echo "<h3>Unable to connect to database due to following error(s). Please check details in configuration file.</h3>";
			if (!defined('DEV_MODE') || (defined('DEV_MODE') && DEV_MODE != '1')){
				ini_set('display_errors','On');
				echo sql_connect_error($dbh);
				ini_set('display_errors','Off');
			}
			header('HTTP/1.1 503 Service Temporarily Unavailable');
			header('Status: 503 Service Temporarily Unavailable');
			header('Retry-After: 10');  /* 10 seconds */
			exit();
		}
	}
}


function sql_connect_errno($dbh){
	/*This function returns the database connectivity error no.*/
	global $dbms;

	if($dbms == 'mssql' || $dbms == 'pgsql'){
		return !$dbh;
	} else {
		return mysqli_connect_errno($dbh);
	}
}

function sql_connect_error($dbh){
	/*This function returns the database connectivity error*/
	global $dbms;

	if($dbms == 'mssql' || $dbms == 'pgsql'){
		return $dbh;
	} else {
		return mysqli_connect_error($dbh);
	}
}

function sql_select_db($dbh, $db_name){
	/*This function is used to select current database*/
	global $dbms;

	if($dbms == 'mssql'){

	} else {
		mysqli_select_db($dbh,$db_name);
	}
}

function sql_query($key, $params = array(), $direct = 0){
	/*This function is used to execute the queries*/
	global $dbms, $dbh;

	if($direct == 1){
		$sql = $key;
	} else {
		$sql = sql_getQuery($key, $params);
	}

	if(empty($sql)){
		return false;
	}

	if($dbms == 'mssql'){
		$result = sqlsrv_query($dbh, $sql, array(), array("Scrollable"=>"buffered"));
	}elseif($dbms == 'pgsql'){
		$result = pg_query($dbh, $sql);
	}else {
		$result = mysqli_query($dbh, $sql);
	}
	if(!$result){
		$error = "Key: ".$key." \t SQL: ".$sql." \t Error: ".sql_error($dbh);
		trigger_error($error, E_USER_ERROR);
	}

	return $result;
}

function sql_getQuery($key, $params = array()){
	/*This function is used to fetch the required query from sql.php */
	global $sql_queries;

	$sql = $sql_queries[$key];
	$excludeEscapeforKeys = array('querystring', 'timestampCondition', 'condition', 'prepend', 'prependCondition', 'guestpart', 'originalsql', 'extra', 'sqlpart', 'insertvalues', 'limitClause', 'usertable', 'set', 'userids','avatarfield');
	if(!empty($params)){
		foreach ($params as $key => $value) {
			$search = '{'.$key.'}';
			$replace = sql_real_escape_string($value);
			if(in_array($key, $excludeEscapeforKeys)){
				$replace = $value;
			}
			$sql =  str_replace($search, $replace, $sql);
		}
	}
	return $sql;
}

function sql_error($dbh){
	/*This function returns the error in query execution */
	global $dbms;
	if($dbms == 'pgsql'){
		return pg_last_error($dbh);
	} else if($dbms == "mssql"){
		return sqlsrv_error($dbh);
	} else {
		return mysqli_error($dbh);
	}
}

function sql_fetch_assoc($query){
	/*This function is used to fetch result set for query */
	global $dbms;
	if($dbms == 'mssql'){
		return sqlsrv_fetch_array($query);
	} else if($dbms == 'pgsql'){
		return pg_fetch_array($query);
	} else {
		return mysqli_fetch_assoc($query);
	}
}

function sql_real_escape_string($text){
	global $dbms;

	if($dbms == 'mssql'){
		return sqlsrv_real_escape_string($text);
	} else if($dbms == 'pgsql'){
		return pg_escape_string($GLOBALS['dbh'],$text);
	}else {
		return mysqli_real_escape_string($GLOBALS['dbh'], $text);
	}
}

function sql_insert_id($tablename, $column = 'id'){
	global $dbms;


	if($dbms == 'mssql'){
		$sql = "select max([".$column."]) from ".$tablename;

		$query = sql_query($sql, array(), 1);
        if (defined('DEV_MODE') && DEV_MODE == '1') { echo sql_error($GLOBALS['dbh']); }
        $row=sql_fetch_assoc($query);
        return $row[0];
	} else if($dbms == 'pgsql'){
		$sql = "select max(id) from ".$tablename;
		$query = sql_query($sql, array(), 1);
        if (defined('DEV_MODE') && DEV_MODE == '1') { echo sql_error($GLOBALS['dbh']); }
        $row=sql_fetch_assoc($query);
        return $row[0];
	} else {
		return mysqli_insert_id($GLOBALS['dbh']);
	}
}

function sql_num_rows($query){
	if($GLOBALS['dbms'] == "mssql"){
		return sqlsrv_num_rows($query);
	}else if($GLOBALS['dbms'] == "pgsql"){
		return pg_num_rows($query);
	} else {
		return mysqli_num_rows($query);
	}
}


function sql_multi_query($dbh, $sql){
	$result = array();
	$sqlarr = explode(';', $sql);
	foreach ($sqlarr as $sql){
		if($GLOBALS['dbms'] == "mssql"){
			$result[]  = sqlsrv_query($dbh, $sql);
		}elseif($GLOBALS['dbms'] == "pgsql"){
			$result[] = pg_query($dbh, $sql);
		}else{
			$result[]  = mysqli_query($dbh, $sql);
		}
	}
	return $result;
}

function sql_affected_rows($query){
	if($GLOBALS['dbms'] == 'mssql'){
		return sqlsrv_rows_affected($dbh);
	}elseif($GLOBALS['dbms'] == 'pgsql'){
		return pg_affected_rows($query);
	}
	else{
		return mysqli_affected_rows($GLOBALS['dbh']);
	}
}


function sql_fetch_array($result){
	if($GLOBALS['dbms'] == 'mssql')
		return sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC);
	else
		return mysqli_fetch_array($result);
}

function cometchatDBConnect(){
	sql_connect();
	if($GLOBALS['dbms'] != 'mssql' && $GLOBALS['dbms'] != 'pgsql'){
		sql_select_db($GLOBALS['dbh'],DB_NAME);
		sql_query('setNames');
		sql_query('setCharacter');
		sql_query('setCollationConnection');
	}
}

function cometchatMemcacheConnect(){
	global $writable;
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."cometchat_cache.php");
	global $memcache;
	if(defined('MEMCACHE') && MEMCACHE!=0 && MC_NAME=='memcachier'){
		$memcache = new MemcacheSASL();
		$memcache->addServer(MC_SERVER,MC_PORT);
		$memcache->setSaslAuthData(MC_USERNAME,MC_PASSWORD);
	}elseif(defined('MEMCACHE') && MEMCACHE!=0){
		phpFastCache::setup("path",dirname(__FILE__).DIRECTORY_SEPARATOR.'writable'.DIRECTORY_SEPARATOR.$writable);
		phpFastCache::setup("storage",MC_NAME);
		$memcache = phpFastCache();
	}
}

function settingsCacheConnect(){
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."cometchat_cache.php");
	global $settingscache;
	global $settings;
	global $client;
	global $writable;
	global $sql_queries;

	phpFastCache::setup("path",dirname(__FILE__).DIRECTORY_SEPARATOR.'writable'.DIRECTORY_SEPARATOR.$writable);
	phpFastCache::setup("storage",'files');
	$settingscache = phpFastCache();

	if ($conf = getCachedSettings($client.'settings', 3600)) {
		$settings = unserialize($conf);
	}else {
		cometchatDBConnect();
		$settings = array();
		$query = sql_query('cometchat_settings');
		if (!sql_error($GLOBALS['dbh'])) {
			while ($setting = sql_fetch_assoc($query)) {
				$settings[$setting['setting_key']] = $setting;
			}
			setCachedSettings($client.'settings',serialize($settings),3600);
		}

	}
}

function setConfigValue($key,$val){
	global $settings;
	if(empty($settings[$key])){
		return $val;
	}else{
		if($settings[$key]['key_type']==2){
			return unserialize($settings[$key]['value']);
		}else{
			if($settings[$key]['setting_key'] == 'color'){
				return getDefaultColor($settings[$key]['value']);
			}else{
				return $settings[$key]['value'];
			}
		}
	}
}

function getDefaultColor($color){
	cometchatDBConnect();
	$query = sql_query('getDefaultColor',array('color'=>$color));
    if($result = sql_fetch_assoc($query)){
    	return $result['color'];
	}
}

function getParentColor($color){
	cometchatDBConnect();
    $query = sql_query('getParentColor',array('color'=>$color, 'color_key'=>'parentColor'));
    if($result = sql_fetch_assoc($query)){
    	return $result['color_value'];
    }
}

function setNewLanguageValue($language,$code,$type,$name){
	global $languages;
	if(!empty($languages[$code][$type][$name])&&!empty($languages[$code][$type])&&!empty($languages[$code])&&!empty($language)){
		return array_merge($language,$languages[$code][$type][$name]);
	}
	return $language;
}

function setNewColorValue($color_array,$color_name){
	cometchatDBConnect();
	$result = sql_query('setNewColorValue',array('color'=>$color_name));
	$newval = array();
	if(!empty($result)){
		while($data = sql_fetch_assoc($result)){
			if($data['color_key']!='parentColor'){
	        	$newval[$data['color_key']] = $data['color_value'];
			}
	    }
	    return array_merge($color_array,$newval);
	}
}

function setLanguageValue($key,$val,$code,$type,$name){
	global $languages;
	if(empty($languages[$code][$type][$name][$key])){
		return $val;
	}else{
		return stripslashes($languages[$code][$type][$name][$key]);
	}
}

function setColorValue($key,$val){
	global $colors;
	global $color;
	$colorval = unserialize($colors[$color][$color]);
	if(empty($colorval[$key])){
		return $val;
	}else{
		return '#'.$colorval[$key];
	}
}

function getLanguageVar(){
	global $client;
	global $writable;
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."cometchat_cache.php");
	global $settingscache;
	global $languages;

	phpFastCache::setup("path",dirname(__FILE__).DIRECTORY_SEPARATOR.'writable'.DIRECTORY_SEPARATOR.$writable);
	phpFastCache::setup("storage",'files');
	$settingscache = phpFastCache();

	if ($conf = getCachedSettings($client."cometchat_language", 3600)) {
		$languages = unserialize($conf);
	}else {
		cometchatDBConnect();
		$languages = array();
		$query = sql_query('getLanguageVar');

		while ($lang = sql_fetch_assoc($query)) {
			if(empty($languages[$lang['code']])){
				$languages[$lang['code']] = array();
			}
			if(empty($languages[$lang['code']][$lang['type']])){
				$languages[$lang['code']][$lang['type']] = array();
			}
			if(empty($languages[$lang['code']][$lang['type']][$lang['name']])){
				$languages[$lang['code']][$lang['type']][$lang['name']] = array();
			}
			$languages[$lang['code']][$lang['type']][$lang['name']][$lang['lang_key']] = $lang['lang_text'];
		}
		setCachedSettings($client."cometchat_language",serialize($languages),3600);
	}
}

function getColorVars(){
	global $client;
	global $writable;
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."cometchat_cache.php");
	global $settingscache;
	global $colors;

	phpFastCache::setup("path",dirname(__FILE__).DIRECTORY_SEPARATOR.'writable'.DIRECTORY_SEPARATOR.$writable);
	phpFastCache::setup("storage",'files');
	$settingscache = phpFastCache();

	if ($conf = getCachedSettings($client."cometchat_color", 3600)) {
		$colors = unserialize($conf);
	}else {
		cometchatDBConnect();
		$colors = array();
		$query = sql_query('getColorVars');

		while ($color = sql_fetch_assoc($query)) {
			if(empty($colors[$color['color']])){
				$colors[$color['color']] = array();
			}
			$colors[$color['color']][$color['color_key']] = $color['color_value'];
		}
		setCachedSettings($client."cometchat_color",serialize($colors),3600);
	}
}

function getBotList() {
	global $client;
	global $writable;
	global $chromeReorderFix;
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."cometchat_cache.php");
	global $settingscache;

	phpFastCache::setup("path",dirname(__FILE__).DIRECTORY_SEPARATOR.'writable'.DIRECTORY_SEPARATOR.$writable);
	phpFastCache::setup("storage",'files');
	$settingscache = phpFastCache();

	if ($conf = getCachedSettings($client."cometchat_bots", 3600)) {
		$bots = unserialize($conf);
	}else {
		cometchatDBConnect();
		$bots = array();
		$query = sql_query('getBotList');
		while ($bot = sql_fetch_assoc($query)) {
			$id = $chromeReorderFix.$bot['id'];
			$bots[$id]["id"] = $bot['id'];
			$bots[$id]["n"] = $bot['name'];
			$bots[$id]["d"] = $bot['description'];
			$bots[$id]["a"] = $bot['avatar'];
			$bots[$id]["api"] = $bot['apikey'];
		}
		setCachedSettings($client."cometchat_bots",serialize($bots),3600);
	}
	return $bots;
}

function mapLanguageKeys($language,$key_mapping,$type,$name){
	global $lang;
	if(!defined('CCADMIN')){
		$language = setNewLanguageValue($language,$lang,$type,$name);
		foreach ($key_mapping as $key => $value) {
			$language[$key] = $language[$value];
			/*unset($language[$value]);*/
		}
	}
	return $language;
}

function reverseMapLanguageKeys($language,$key_mapping){
	$key_mapping = array_flip($key_mapping);
	foreach ($key_mapping as $key => $value) {
		$language[$key] = $language[$value];
		unset($language[$value]);
	}
	return $language;
}


function getCachedSettings($key){
	if(!empty($GLOBALS['settingscache']) && method_exists($GLOBALS['settingscache'], 'get')){
		return $GLOBALS['settingscache']->get($key);
	}
}

function setCachedSettings($key,$contents,$timeout = 60){
	if (empty($contents) || empty($key)) {
		return false;
	}
	removeCachedSettings($key);
	if(!empty($GLOBALS['settingscache']) && method_exists($GLOBALS['settingscache'], 'set')){
		$GLOBALS['settingscache']->set($key,$contents,$timeout);
	}
}

function removeCachedSettings($key){
	if (empty($key)) {
		return;
	}
	if(!empty($GLOBALS['settingscache']) && method_exists($GLOBALS['settingscache'], 'delete')){
		$GLOBALS['settingscache']->delete($key);
	}
}

function clearcache($src) {
	global $client;
	global $server;
	if ($handle = opendir($src)) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != ".." && $file != "index.html") {
				if (is_dir($src.DIRECTORY_SEPARATOR.$file) ) {
					clearcache($src.DIRECTORY_SEPARATOR.$file);
				}else{
					@unlink($src.DIRECTORY_SEPARATOR.$file);
				}
			}
		}
	}
}

function sanitize($text) {
	global $smileys_sorted;

	$temp = $text;
	$text = sanitize_core($text);
	$text = $text." ";
	$text = str_replace('&amp;','&',$text);

	$search = "/((?#Email)(?:\S+\@)?(?#Protocol)(?:(?:ht|f)tp(?:s?)\:\/\/|~\/|\/)?(?#Username:Password)(?:\w+:\w+@)?(?#Subdomains)(?:(?:[-\w]+\.)+(?#TopLevel Domains)(?:com|org|net|gov|mil|biz|info|mobi|name|aero|jobs|museum|travel|a[cdefgilmnoqrstuwz]|b[abdefghijmnorstvwyz]|c[acdfghiklmnoruvxyz]|d[ejkmnoz]|e[ceghrst]|f[ijkmnor]|g[abdefghilmnpqrstuwy]|h[kmnrtu]|i[delmnoqrst]|j[emop]|k[eghimnprwyz]|l[abcikrstuvy]|m[acdghklmnopqrstuvwxyz]|n[acefgilopruz]|om|p[aefghklmnrstwy]|qa|r[eouw]|s[abcdeghijklmnortuvyz]|t[cdfghjkmnoprtvwz]|u[augkmsyz]|v[aceginu]|w[fs]|y[etu]|z[amw]|aero|arpa|biz|com|coop|edu|info|int|gov|mil|museum|name|net|org|pro))(?#Port)(?::[\d]{1,5})?(?#Directories)(?:(?:(?:\/(?:[-\w~!$+|.,=]|%[a-f\d]{2}|[&])+)+|\/)+|#)?(?#Query)(?:(?:\?(?:[-\w~!$+|\/.,*:]|%[a-f\d{2}])+=?(?:[-\w~!$+|.,*:=]|%[a-f\d]{2})*)(?:&(?:[-\w~!$+|.,*:]|%[a-f\d{2}])+=?(?:[-\w~!$+|.,*:=]|%[a-f\d]{2})*)*)*(?#Anchor)(?:#(?:[-\w~!$+|\/.,*:=]|%[a-f\d]{2})*)?)([^[:alpha:]]|\?)/i";

	if (DISABLE_LINKING != 1) {
		$text = preg_replace_callback($search, "autolink", $text);
	}
	if (DISABLE_SMILEYS != 1) {

		foreach ($smileys_sorted as $pattern => $result) {
			$title = str_replace("-"," ",ucwords(preg_replace("/\.(.*)/","",$result)));
			$class = str_replace("-"," ",preg_replace("/\.(.*)/","",$result));
			$text = str_replace(str_replace('&amp;','&',htmlspecialchars($pattern, ENT_NOQUOTES)).' ','<img class="cometchat_smiley" height="20" width="20" src="'.BASE_URL.'writable/images/smileys/'.$result.'" title="'.$title.'"> ',$text.' ');
		}
	}
	return trim($text);
}

function sanitize_core($text) {
	global $bannedWords;
	$text = htmlspecialchars($text, ENT_NOQUOTES);
	$text = str_replace("\n\r","\n",$text);
	$text = str_replace("\r\n","\n",$text);
	$text = str_replace("\n"," <br> ",$text);

	for ($i=0;$i < count($bannedWords);$i++) {
		$text = str_ireplace(' '.$bannedWords[$i].' ',' '.$bannedWords[$i][0].str_repeat("*",strlen($bannedWords[$i])-1).' ',' '.$text.' ');
	}
	$text = trim($text);
	return $text;
}

function autolink($matches) {

	$link = $matches[1];

	if (preg_match("/\@/",$matches[1])) {
		$text = "<a href=\"mailto: {$link}\">{$matches[0]}</a>";
	} else {
		if (!preg_match("/(file|gopher|news|nntp|telnet|http|ftp|https|ftps|sftp):\/\//",$matches[1])) {
			$link = "http://".$matches[1];
		}

		if (DISABLE_YOUTUBE != 1 && preg_match('#(?:<\>]+href=\")?(?:http://)?((?:[a-zA-Z]{1,4}\.)?youtube.com/(?:watch)?\?v=(.{11}?))[^"]*(?:\"[^\<\>]*>)?([^\<\>]*)(?:)?#',$link,$match)) {

			/*

			// Bandwidth intensive function to fetch details about the YouTube video

			$contents = file_get_contents("http://gdata.youtube.com/feeds/api/videos/{$match[2]}?alt=json");

			$data = json_decode($contents);
			$title = $data->entry->title->{'$t'};

			if (strlen($title) > 50) {
				$title = substr($title,0,50)."...";
			}

			$description = substr($data->entry->content->{'$t'},0,100);
			$length = seconds2hms($data->entry->{'media$group'}->{'yt$duration'}->seconds);
			$rating = $data->entry->{'gd$rating'}->average;

			*/

			$text = '<a href="'.$link.'" target="_blank">'.$link.'</a><br/><a href="'.$link.'" target="_blank" style="display:inline-block;margin-bottom:3px;margin-top:3px;"><img src="http://img.youtube.com/vi/'.$match[2].'/default.jpg" border="0" style="padding:0px;display: inline-block; width: 120px;height:90px;">
			<div style="margin-top:-30px;text-align: right;width:110px;margin-bottom:10px;">
			<img height="20" border="0" width="20" style="opacity: 0.88;" src="'.BASE_URL.'images/play.gif"/>
			</div></a>';

		} else {
			$text = $matches[1];

			if (strlen($matches[1]) > 30) {
				$left = substr($matches[1],0,22);
				$right = substr($matches[1],-5);
				$matches[1] = $left."...".$right;
			}

			$text = "<a href=\"{$link}\" target=\"_blank\" title=\"{$text}\">{$matches[1]}</a>$matches[2]";
		}
	}


	return $text;
}

function seconds2hms ($sec, $padHours = true) {
	$hms = "";
	$hours = intval(intval($sec) / 3600);
	if ($hours != 0) {
		$hms .= ($padHours) ? str_pad($hours, 2, "0", STR_PAD_LEFT). ':' : $hours. ':';
	}

	$minutes = intval(($sec / 60) % 60);
	$hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ':';
$seconds = intval($sec % 60);
	$hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);
	return $hms;
}

function decode_controlmessage($message){
	if(strpos($message,'CC^CONTROL_') !== false){
		$message = str_ireplace('CC^CONTROL_','',$message);
		$cc_array = json_decode($message, true);
		return $cc_array;
	}else{
		return array();
	}
}

function encode_controlmessage($cc_array){
	if(!empty($cc_array)){
		$cc_array = json_encode($cc_array);
		$cc_array = 'CC^CONTROL_'.$cc_array;
		return $cc_array;
	}else{
		return '';
	}
}

function sendMessageTo($to,$message) {
	$response = sendMessage($to,$message,1);
	pushMobileNotification($to,$response['id'],$response['m']);
}

function sendSelfMessage($to,$message,$sessionMessage = '') {
	return sendMessage($to,$message,2);
}

function sendMessage($to,$message,$dir = 0,$type = '') {
	global $userid;
	global $cookiePrefix;
	global $chromeReorderFix;
	global $plugins;
	global $blockpluginmode;
	global $bannedUserIDs;
	global $bannedMessage;
	global $usebots;
	global $disableRecentTab;
	$stickersflag = 0;
	$voicenoteflag = 0;
	$botflag = 0;
	$origmessage = $message;
	$localmessageid = '';

	if(in_array($userid,$bannedUserIDs)) {
		$message = sanitize($bannedMessage);
		$dir = 2;
	}
	if($dir === 0 && (empty($type) || ($type != 'filetransfer' && $type != 'handwrite' && $type != 'voicenote' && $type != 'botresponse'))) {
		if(!isset($_REQUEST['deny_sanitize']))
			$message = sanitize($message);
	}

	$block = 0;
	if (in_array('block',$plugins)) {
		$blockedIds = getBlockedUserIDs(0,1);
		if(in_array($to,$blockedIds)){
			$block = 2;
			if(!empty($_REQUEST['callbackfn']) && $_REQUEST['callbackfn']=='mobileapp'){
				$response = array();
				$response['id'] = "-1";
				$response['m'] = "You are blocked";
				sendCCResponse(json_encode($response));
			}
			if($blockpluginmode == 1 && in_array($to,$blockedIds)){
				if( $dir == 1){
					$dir = 3;
				} else {
					$dir = 2;
				}
			} else {
				return '';
			}
		}
	}
	if (!empty($to) && isset($message) && $message!='' && $userid > 0) {
		if(strpos($message,'CC^CONTROL_') !== false){
			$message = str_ireplace('CC^CONTROL_','',$message);
			$controlparameters = json_decode($message,true);
			switch($controlparameters['name']){
				case 'avchat':
					switch($controlparameters['method']){
						case 'endcall':
							$message = 'CC^CONTROL_PLUGIN_AVCHAT_ENDCALL_'.$controlparameters['params']['grp'];
						break;
						case 'rejectcall':
							$message = 'CC^CONTROL_PLUGIN_AVCHAT_REJECTCALL_'.$controlparameters['params']['grp'];
						break;
						case 'noanswer':
							$message = 'CC^CONTROL_PLUGIN_AVCHAT_NOANSWER_'.$controlparameters['params']['grp'];
						break;
						case 'canceloutgoingcall':
							$message = 'CC^CONTROL_PLUGIN_AVCHAT_CANCELCALL_'.$controlparameters['params']['grp'];
						break;
						case 'busycall':
							$message = 'CC^CONTROL_PLUGIN_AVCHAT_BUSYCALL_'.$controlparameters['params']['grp'];
						break;
						case 'initiatecall':
							$message = 'CC^CONTROL_PLUGIN_AVCHAT_INITIATECALL_'.$controlparameters['params']['grp'].'_'.$controlparameters['params']['chatroommode'].'_'.$controlparameters['params']['caller'].'_'.$controlparameters['params']['direction'];
						break;
						default :
							$message = '';
						break;
					}
					break;
				case 'audiochat':
					switch($controlparameters['method']){
						case 'endcall':
							$message = 'CC^CONTROL_PLUGIN_AUDIOCHAT_ENDCALL_'.$controlparameters['params']['grp'];
						break;
						case 'rejectcall':
							$message = 'CC^CONTROL_PLUGIN_AUDIOCHAT_REJECTCALL_'.$controlparameters['params']['grp'];
						break;
						case 'noanswer':
							$message = 'CC^CONTROL_PLUGIN_AUDIOCHAT_NOANSWER_'.$controlparameters['params']['grp'];
						break;
						case 'canceloutgoingcall':
							$message = 'CC^CONTROL_PLUGIN_AUDIOCHAT_CANCELCALL_'.$controlparameters['params']['grp'];
						break;
						case 'busycall':
							$message = 'CC^CONTROL_PLUGIN_AUDIOCHAT_BUSYCALL_'.$controlparameters['params']['grp'];
						break;
						default :
							$message = '';
						break;
					}
					break;
				case 'broadcast':
					switch($controlparameters['method']){
						case 'endcall':
							$message = 'CC^CONTROL_PLUGIN_BROADCAST_ENDCALL_'.$controlparameters['params']['grp'];
						break;
						default :
							$message = '';
						break;
					}
					break;
				case 'stickers':
						$stickersflag = 1;
						$message = 'CC^CONTROL_'.$message;
					break;
				case 'bots':
						$botflag = 1;
						$message = 'CC^CONTROL_'.$message;
					break;
				case 'smilies':
					$message = sanitize($message);
					break;
				case 'voicenote':
						$voicenoteflag = 1;
						$message = 'CC^CONTROL_'.$message;
					break;
				default :
					break;
			}
		}

		if($dir === 0){
			if($stickersflag == 0 && $voicenoteflag == 0 && $botflag == 0){
				$message = str_ireplace('CC^CONTROL_','',$message);
			}
		}

		if(function_exists('hooks_processMessageBefore')){
			$message = hooks_processMessageBefore(array('to' => $to, 'message' => $message, 'dir' => $dir));
		}

		if (!empty($_REQUEST['callback'])) {
		    if (!empty($_SESSION['cometchat']['duplicates'][$_REQUEST['callback']])) {
		        exit;
		    }
		    $_SESSION['cometchat']['duplicates'][$_REQUEST['callback']] = 1;
		}
		$old=0;

		$timestamp = getTimeStamp();
		$insertedid = 0;

		if(!empty($_REQUEST['localmessageid'])){
			$localmessageid = $_REQUEST['localmessageid'];
		}
		if(empty($localmessageid) || (!empty($localmessageid) && empty($_SESSION['cometchat']['duplicates']['localmessageid'][$localmessageid]))){
			$query = sql_query('insertMessage',array('userid'=>$userid, 'to'=>$to, 'message'=>$message, 'timestamp'=>$timestamp, 'old'=>$old, 'dir'=>$dir));
			if (defined('DEV_MODE') && DEV_MODE == '1') { echo sql_error($GLOBALS['dbh']); }
			$insertedid = sql_insert_id('cometchat');
			if(!empty($localmessageid)){
				$_SESSION['cometchat']['duplicates']['localmessageid'][$localmessageid] = $insertedid;
			}

			if($disableRecentTab == 0 && $userid > 0 && $to > 0) {
				$convo_hash = $userid < $to? md5(md5($userid).md5($to)) : md5(md5($to).md5($userid));
				$query = sql_query('insertRecentConversation',array('insertedid'=>$insertedid, 'userid'=>$userid, 'to'=>$to, 'message'=>$message, 'timestamp'=>$timestamp, 'convo_hash'=>$convo_hash));
				if (defined('DEV_MODE') && DEV_MODE == '1') { echo sql_error($GLOBALS['dbh']); }
			}
		} else if(!empty($localmessageid)){
			$insertedid = $_SESSION['cometchat']['duplicates']['localmessageid'][$localmessageid];
		}

		$response = array("id" => $insertedid, "m" => $message, "from" => $to, "direction" => $dir, "sent" => $timestamp, "localmessageid" => $localmessageid);

		if(function_exists('hooks_processMessageAfter')){
			$message = hooks_processMessageAfter(array('id' => $insertedid, 'to' => $to, 'message' => $message, 'dir' => $dir));
		}

		if (USE_COMET == 1) {
			$key = '';
			if( defined('KEY_A') && defined('KEY_B') && defined('KEY_C') ){
				$key = KEY_A.KEY_B.KEY_C;
			}
			$response['cs'] = array();
			if($dir <> 3) {
				$key_prefix = ($dir === 2) ? $userid:$to;
				$from = ($dir === 2) ? $to:$userid;
				$self = ($dir === 2) ? 1 : 0;
				$channel = md5($key_prefix.$key);
				$dir_self = $dir_nonself = array();
				$comet = new Comet(KEY_A,KEY_B);
				if(method_exists($comet, 'processChannel')){
					$channel = processChannel($channel);
				}
				$localmessageid = !empty($localmessageid)? $localmessageid: ((!empty($_GET['callback'])) ? $_GET['callback'] : '');

				$dir_nonself['info'] = $comet->publish(array(
					'channel' => $channel,
					'message' => array ( "id" => $insertedid, "from" => $from, "message" => ($message), "sent" => $timestamp, "self" => $self, "direction" => $dir, "localmessageid" => $localmessageid)
				));
				$dir_nonself['channel'] = $channel;
				if($dir == 0 && ((isset($_REQUEST['cc_direction']) && $_REQUEST['cc_direction'] == 0) ||  CS_MESSAGE_SYNC == '1')){
					$key_prefix = $userid;
					$channel = md5($key_prefix.$key);
					$self = 1;
					$dir_self['info'] = $comet->publish(array(
						'channel' => $channel,
						'message' => array ( "id" => $insertedid, "from" => $to, "message" => ($message), "sent" => $timestamp, "self" => $self, "direction" => 0, "localmessageid" => $localmessageid)
					));
					$dir_self['channel'] = $channel;
				}
				if(defined('DEV_MODE') && DEV_MODE == '1'){
					$response['cs']['dir_nonself'] = $dir_nonself;
					$response['cs']['dir_self'] = $dir_self;
				}
			}

		}

		if (empty($_SESSION['cometchat']['cometchat_user_'.$to])) {
			$_SESSION['cometchat']['cometchat_user_'.$to] = array();
		}
		if ($dir <> 1){
			$_SESSION['cometchat']['cometchat_user_'.$to][$chromeReorderFix.$insertedid] = array("id" => $insertedid, "from" => $to, "message" => $response['m'], "self" => 1, "old" => 1, 'sent' => $timestamp, 'direction' => $dir, "localmessageid" => $localmessageid);
		}
		$flag =0;
		if(strpos($message,'jqcc.cometchat.joinChatroom')!=false){
			$flag =1;
			$messages = (explode(".",$message));
			$message = $messages[0];
			if (function_exists('hooks_message')){
				hooks_message($userid,$to,$message,$dir);
				return $response;
			}
		}
		if (function_exists('hooks_message')) {
			hooks_message($userid,$to,$response['m'],0);
		}
   		return $response;
	}
}

function broadcastMessage($broadcast) {
	global $userid;
	global $cookiePrefix;
	global $chromeReorderFix;
	global $plugins;
	global $blockpluginmode;
	global $usebots;

	if(empty($_SESSION['cometchat'])||empty($_SESSION['cometchat']['user'])||empty($_SESSION['cometchat']['user']['n'])){
		getStatus();
	}
	if (in_array('block',$plugins)) {
		$blockedIds = getBlockedUserIDs(0,1);
		for ($i=0; $i < sizeof($broadcast); $i++) {
			if($blockpluginmode == 1 && in_array($broadcast[$i]['to'],$blockedIds)){
				if( $broadcast[$i]['dir'] == 1){
					$broadcast[$i]['dir'] = 3;
				} else {
					$broadcast[$i]['dir'] = 2;
				}
			} else if(in_array($broadcast[$i]['to'],$blockedIds)){
				array_splice($broadcast, $i,1);
			}
		}
	}

	if (!empty($broadcast) && $userid > 0) {
		for ($i=0; $i < sizeof($broadcast); $i++) {
			if( empty($broadcast[$i]['to'])	|| !isset($broadcast[$i]['message']) || $broadcast[$i]['to'] == '' || $broadcast[$i]['message'] == ''){
				array_splice($broadcast, $i,1);
			}
			if($broadcast[$i]['dir'] === 0){
				$broadcast[$i]['message'] = str_ireplace('CC^CONTROL_','',$broadcast[$i]['message']);
			}
			sanitize($broadcast[$i]['message']);
		}
	}
	if (!empty($broadcast) && $userid > 0) {
		$sizeof_broadcast=sizeof($broadcast);
		if (!empty($_REQUEST['callback'])) {
			if (!empty($_SESSION['cometchat']['duplicates'][$_REQUEST['callback']])) {
				exit;
			}
			$_SESSION['cometchat']['duplicates'][$_REQUEST['callback']] = 1;
		}
		$sqlpart = array();
		$newbroadcast = array();
		$send_response = array();
		$insertedid = 0;
		for ($i=0; $i < $sizeof_broadcast; $i++) {
			if(!empty($broadcast[$i]['localmessageid']) && !empty($_SESSION['cometchat']['duplicates']['localmessageid'][$broadcast[$i]['localmessageid']]) ){
				continue;
			}
			$sqlpart[] = "('".sql_real_escape_string($userid)."', '".sql_real_escape_string($broadcast[$i]['to'])."','".sql_real_escape_string($broadcast[$i]['message'])."','".sql_real_escape_string(getTimeStamp())."',0,".sql_real_escape_string($broadcast[$i]['dir']).")";
			array_push($newbroadcast, $broadcast[$i]);
		}
		if(!empty($sqlpart)){
			$sqlpart = implode(",", $sqlpart);
			$query = sql_query('insertBroadcastMessages',array('sqlpart'=>$sqlpart));

			if (defined('DEV_MODE') && DEV_MODE == '1') { echo sql_error($GLOBALS['dbh']); }

			$insertedid = sql_insert_id('cometchat');
		}
		$sizeof_broadcast=sizeof($newbroadcast);
		for ($i=0; $i < $sizeof_broadcast; $i++) {
			$response = array("id" => $insertedid+$i, "m" => $newbroadcast[$i]['message'], "from"=> $newbroadcast[$i]['to'], "direction" => $newbroadcast[$i]['dir'], "localmessageid" => $newbroadcast[$i]['localmessageid']);
			array_push($send_response, $response);
			if(!empty($newbroadcast[$i]['localmessageid'])){
				$_SESSION['cometchat']['duplicates']['localmessageid'][$newbroadcast[$i]['localmessageid']] = $insertedid+$i;
			}

			if(!defined('USE_COMET') || USE_COMET == 0 || (USE_COMET == 1 && strlen($insertedid)<13)){
				if (empty($_SESSION['cometchat']['cometchat_user_'.$newbroadcast[$i]['to']])) {
					if (empty($_SESSION['cometchat']['cometchat_user_'.$newbroadcast[$i]['to'].'_clear'])) {
						unset($_SESSION['cometchat']['cometchat_user_'.$newbroadcast[$i]['to'].'_clear']);
					}
					$_SESSION['cometchat']['cometchat_user_'.$newbroadcast[$i]['to']] = array();
				}
				if($newbroadcast[$i]['dir']!=1){
					$_SESSION['cometchat']['cometchat_user_'.$newbroadcast[$i]['to']][$chromeReorderFix.($insertedid+$i)] = array("id" => ($insertedid+$i), "from" => $newbroadcast[$i]['to'], "message" => $newbroadcast[$i]['message'], "self" => 1, "old" => 1, 'sent' => (getTimeStamp()), 'direction' => $newbroadcast[$i]['dir']);
				}

				if (function_exists('hooks_message')) {
					hooks_message($userid,$newbroadcast[$i]['to'],$newbroadcast[$i]['message'],$newbroadcast[$i]['dir']);
				}
			}
		}
		if(!defined('DEV_MODE') || DEV_MODE == '0'){
			header('content-type: application/json; charset=utf-8');
			sendCCResponse(json_encode($send_response));
		}

		if(USE_COMET == '1' && !empty($send_response)){
			publishCometMessages($newbroadcast,$send_response[0]['id']);
		}

		foreach ($send_response as $rkey => $rvalue) {
			$send_response['push'][$rkey]=pushMobileNotification($rvalue['from'],$rvalue['id'],$_SESSION['cometchat']['user']['n'].": ".$rvalue['m']);
			if(strpos($rvalue['m'],'@') === 0 && $usebots) {
				checkBotMessage($rvalue['from'], $rvalue['m'], 0);
			}
		}

		if(defined('DEV_MODE') && DEV_MODE == '1'){
			header('content-type: application/json; charset=utf-8');
			sendCCResponse(json_encode($send_response));
		}
	}
}

function getBlockedUserIDs($receive=0,$send=0){
	global $plugins;
	global $userid;
	global $blockpluginmode;
	$blockedIds = array();

	if (in_array('block',$plugins)) {
		$querystring = "";
		if($send == 1){
			$querystring = sql_getQuery('getBlockedUserIDs_subquery',array('userid'=>$userid));
		}

		if($receive == 0) {
			if(!is_array($blockedIds = getCache('blocked_id_of_'.$userid))){
				$blockedIds = array();
				$query = sql_query('getBlockedUserIDs_send',array('querystring'=>$querystring, 'userid'=>$userid));
				$blockedId = sql_fetch_assoc($query);
				if (!empty($blockedId['blockedids'])) {
					$blockedIds = explode(',',$blockedId['blockedids']);
				}
				setCache('blocked_id_of_'.$userid,$blockedIds,3600);
			}
		} else if(!is_array($blockedIds = getCache('blocked_id_of_receive_'.$userid)) && $receive == 1) {
			$blockedIds = array();
			$query = sql_query('getBlockedUserIDs_receive',array('userid'=>$userid));
			$blockedId = sql_fetch_assoc($query);
			if (!empty($blockedId['blockedids'])) {
				$blockedIds = explode(',',$blockedId['blockedids']);
			}
			setCache('blocked_id_of_receive_'.$userid,$blockedIds,3600);
		}
	}
	return $blockedIds;
}

function publishCometMessages($broadcast,$id) {
	global $userid;
	global $plugins;
	global $blockpluginmode;

	if (in_array('block',$plugins)) {
		$blockedIds = getBlockedUserIDs(0,1);
		for ($i=0; $i < sizeof($broadcast); $i++) {
			if($blockpluginmode == 1 && in_array($broadcast[$i]['to'],$blockedIds)){
				if( $broadcast[$i]['dir'] == 1){
					$broadcast[$i]['dir'] = 3;
				} else {
					$broadcast[$i]['dir'] = 2;
				}
			} else if(in_array($broadcast[$i]['to'],$blockedIds)){
				array_splice($broadcast, $i,1);
			}
		}
	}
	$sizeof_broadcast=sizeof($broadcast);
	for ($i=0; $i < $sizeof_broadcast; $i++) {
		$insertedid = $id+$i;
		$key = '';
		if(defined('KEY_A') && defined('KEY_B') && defined('KEY_C')){
			$key = KEY_A.KEY_B.KEY_C;
		}
		$key_prefix = $broadcast[$i]['dir'] === 2 ? $userid:$broadcast[$i]['to'];
		$channel = md5($key_prefix.$key);

		$from = $broadcast[$i]['dir'] === 2 ? $broadcast[$i]['to']:$userid;
		$self = $broadcast[$i]['dir'] === 2 ? 1 : 0;

		$messagetopost = array ( "id" => $insertedid, "from" => $from, "message" => ($broadcast[$i]['message']), "sent" => getTimeStamp(),"self" => $self,"direction" => $broadcast[$i]['dir']);
		if(!empty( $broadcast[$i]['localmessageid'])){
			$messagetopost['localmessageid'] =  $broadcast[$i]['localmessageid'];
		}

		if($broadcast[$i]['dir'] == 3 || !empty($broadcast[$i]['type'])){
			$channel = md5($userid.$key);
			$self = 1;
			if(!empty($broadcast[$i]['type']) && ($broadcast[$i]['type'] == 'botresponse')) {
				$self = 0;
			}
			$messagetopost['from'] =  $broadcast[$i]['to'];;
			$messagetopost['self'] =  $self;
		}

		$comet = new Comet(KEY_A,KEY_B);
		if(method_exists($comet, 'processChannel')){
			$channel = processChannel($channel);
		}
		$info = $comet->publish(array(
			'channel' => $channel,
			'message' => $messagetopost
		));

		if($broadcast[$i]['dir'] != 1 || $broadcast[$i]['dir'] != 3 && empty($broadcast[$i]['type'])){
			$key_prefix = $userid;
			$channel = md5($key_prefix.$key);
			if(method_exists($comet, 'processChannel')){
				$channel = processChannel($channel);
			}
			$messagetopost['from'] =  $broadcast[$i]['to'];;
			$messagetopost['self'] =  1;
			$messagetopost['direction'] =  0;
			$info = $comet->publish(array(
				'channel' => $channel,
				'message' => $messagetopost
			));
		}
	}
}

function checkBotMessage($to, $message, $chatroommode) {
	global $userid;
	$botlist = getBotList();
	foreach ($botlist as $bot){
		$botcheck = '@'.str_replace(' ', '', strtolower($bot['n']));
		if(stripos($message,$botcheck) === 0){
			$botmessage = trim(str_ireplace($botcheck,'',$message)," ");
			if(!empty($bot['api']) && $botmessage != ''){
				$url = 'http://app.bots.co/api/b/'.$bot['api'];

				if($chatroommode){
					$channel = 'group_'.$userid.'_'.$to;
				} else {
					$channel = 'user_'.$userid.'_'.$to;
				}
				$postdata = json_encode(array('channel' => $channel, 'user' => $userid, 'message' => $botmessage));
				cc_curl_call($url, $postdata);
			}
		}
	}
}

function sendChatroomMessage($to = 0,$message = '',$notsilent = 1,$messagetype='') {
	global $userid;
	global $cookiePrefix;
	global $chromeReorderFix;
	global $bannedUserIDs;
	global $lang;
	global $usebots;
	global $firstguestID;
	$stickersflag = 0;
	$flag = 0;
	$voicenoteflag = 0;
	$botsflag = 0;
	$localmessageid = '';

	if(($to == 0 && empty($_REQUEST['currentroom'])) || ($message == '' && $notsilent == 0) || (isset($_REQUEST['message']) && $_REQUEST['message'] == '') || (empty($userid) && !defined('CCADMIN')) || in_array($userid, $bannedUserIDs)){
		return;
	}
	if (isset($_REQUEST['message']) && !empty($_REQUEST['currentroom'])) {
		$to = sql_real_escape_string($_REQUEST['currentroom']);
		$message = $_REQUEST['message'];
	}

	$origmessage = $message;

	if(isset($message) && $message != '') {
		if(strpos($message,'CC^CONTROL_') !== false){
			$flag = 1;
			$message = str_ireplace('CC^CONTROL_','',$message);
			if($messagetype != 'botresponse'){
				$message = sanitize($message);
			}
			$controlparameters = json_decode($message,true);
			$chatroommode = 1;
			if(isset($controlparameters['params']['chatroommode'])){
				$chatroommode = $controlparameters['params']['chatroommode'];
			}
			switch($controlparameters['name']){
				case 'avchat':
					$grp = $controlparameters['params']['grp'];
					switch($controlparameters['method']){
						case 'endcall':
							$message = 'CC^CONTROL_PLUGIN_AVCHAT_ENDCALL_'.$grp.'_'.$chatroommode;
						break;
						case 'rejectcall':
							$message = 'CC^CONTROL_PLUGIN_AVCHAT_REJECTCALL_'.$grp.'_'.$chatroommode;
						break;
						case 'noanswer':
							$message = 'CC^CONTROL_PLUGIN_AVCHAT_NOANSWER_'.$grp.'_'.$chatroommode;
						break;
						case 'canceloutgoingcall':
							$message = 'CC^CONTROL_PLUGIN_AVCHAT_CANCELCALL_'.$grp.'_'.$chatroommode;
						break;
						case 'busycall':
							$message = 'CC^CONTROL_PLUGIN_AVCHAT_BUSYCALL_'.$grp.'_'.$chatroommode;
						break;
						default :
							$message = '';
						break;
					}
					break;
				case 'audiochat':
					$grp = $controlparameters['params']['grp'];
					switch($controlparameters['method']){
						case 'endcall':
							$message = 'CC^CONTROL_PLUGIN_AUDIOCHAT_ENDCALL_'.$grp.'_'.$chatroommode;
						break;
						case 'rejectcall':
							$message = 'CC^CONTROL_PLUGIN_AUDIOCHAT_REJECTCALL_'.$grp.'_'.$chatroommode;
						break;
						case 'noanswer':
							$message = 'CC^CONTROL_PLUGIN_AUDIOCHAT_NOANSWER_'.$grp.'_'.$chatroommode;
						break;
						case 'canceloutgoingcall':
							$message = 'CC^CONTROL_PLUGIN_AUDIOCHAT_CANCELCALL_'.$grp.'_'.$chatroommode;
						break;
						case 'busycall':
							$message = 'CC^CONTROL_PLUGIN_AUDIOCHAT_BUSYCALL_'.$grp.'_'.$chatroommode;
						break;
						default :
							$message = '';
						break;
					}
					break;
				case 'broadcast':
					$grp = $controlparameters['params']['grp'];
					switch($controlparameters['method']){
						case 'endcall':
							$message = 'CC^CONTROL_PLUGIN_BROADCAST_ENDCALL_'.$grp.'_'.$chatroommode;
						break;
						default :
							$message = '';
						break;
					}
					break;
				case 'stickers':
					$stickersflag = 1;
					$message = 'CC^CONTROL_'.$message;
					break;
				case 'bots':
					$botsflag = 1;
					$message = 'CC^CONTROL_'.$message;
					break;
				case 'voicenote':
					$voicenoteflag = 1;
					$message = 'CC^CONTROL_'.$message;
					break;
				case 'chatroom':
					$delid = $controlparameters['params']['id'];
					switch($controlparameters['method']){
						case 'deletemessage':
							$message = 'CC^CONTROL_deletemessage_'.$delid;
						break;
						case 'kicked':
							$message = 'CC^CONTROL_kicked_'.$delid;
						break;
						case 'banned':
							$message = 'CC^CONTROL_banned_'.$delid;
						break;
						case 'deletedchatroom':
							$message = 'CC^CONTROL_deletedchatroom_'.$delid;
						break;
						default :
							$message = '';
						break;
					}
					break;
				default :
					break;
			}
		}
	}

	if (!empty($_REQUEST['callback'])) {
		if (!empty($_SESSION['cometchat']['duplicates'][$_REQUEST['callback']])) {
			exit;
		}
		$_SESSION['cometchat']['duplicates'][$_REQUEST['callback']] = 1;
	}

	if($notsilent !== 0){
		if($stickersflag == 0 && $voicenoteflag == 0 && $botsflag == 0){
			$message = str_ireplace('CC^CONTROL_','',$message);
		}
	}

	if($flag === 0 && strpos($message, 'class="cc_handwrite_image"') === false && strpos($message, 'class="imagemessage') === false && strpos($message, "javascript:void(0)") === false && strpos($message, 'mediaType="0"') === false){
		$message = sanitize($message);
	}

	if(function_exists('hooks_processGroupMessageBefore')){
		$message = hooks_processGroupMessageBefore(array('to' => $to, 'message' => $message));
	}

	$styleStart = '';
	$styleEnd = '';

	if (!empty($_COOKIE[$cookiePrefix.'chatroomcolor']) && preg_match('/^[a-f0-9]{6}$/i', $_COOKIE[$cookiePrefix.'chatroomcolor']) && $notsilent == 1 && $stickersflag == 0) {
		$styleStart = '<span style="color:#'.sql_real_escape_string($_COOKIE[$cookiePrefix.'chatroomcolor']).'">';
		$styleEnd = '</span>';
	}
	$timestamp = getTimeStamp();

	if (empty($_SESSION['cometchat']['cometchat_chatroom_'.$to])) {
		$_SESSION['cometchat']['cometchat_chatroom_'.$to] = array();
	}

	if(!empty($_REQUEST['localmessageid'])) {
		$localmessageid = $_REQUEST['localmessageid'];
	}

	$insertedid = 0;
	if(empty($localmessageid) || (!empty($localmessageid) && empty($_SESSION['cometchat']['duplicates']['group_localmessageid'][$localmessageid]))){
		$query = sql_query('insertGroupMessage',array('userid'=>$userid, 'to'=>$to, 'styleStart'=>$styleStart, 'message'=>$message, 'styleEnd'=>$styleEnd, 'timestamp'=>$timestamp));
		$insertedid = sql_insert_id('cometchat_chatroommessages');
		if (defined('DEV_MODE') && DEV_MODE == '1') { echo sql_error($GLOBALS['dbh']); }
		if(!empty($_REQUEST['localmessageid'])) {
			$_SESSION['cometchat']['duplicates']['group_localmessageid'][$localmessageid] = $insertedid;
		}
	} else if(!empty($_REQUEST['localmessageid'])) {
		$insertedid = $_SESSION['cometchat']['duplicates']['group_localmessageid'][$localmessageid];
	}

	$response = array("id" => $insertedid,"m" => $styleStart.$message.$styleEnd,"sent" => $timestamp, "localmessageid" =>$localmessageid);

	if (empty($_SESSION['cometchat']['username'])) {
		$name = '';
		$sql = getUserDetails($userid);

		if($userid>$firstguestID) $sql = getGuestDetails($userid);
		$result = sql_query($sql,array(),1);

		if($row = sql_fetch_assoc($result)) {
			if (function_exists('processName')) {
				$row['username'] = processName($row['username']);
			}
			$name = $row['username'];
		}
		$_SESSION['cometchat']['username'] = $name;
	} else {
		$name = $_SESSION['cometchat']['username'];
	}

	$_SESSION['cometchat']['cometchat_chatroom_'.$to][$insertedid] = array('id' => $insertedid, 'from' => $_SESSION['cometchat']['username'], 'fromid' => $userid, 'chatroomid' => $to, 'message' => $styleStart.$message.$styleEnd, 'sent' => ($timestamp), 'localmessageid' => $localmessageid);
	krsort($_SESSION['cometchat']['cometchat_chatroom_'.$to]);

	if($notsilent == 1 && DEV_MODE == 0){
		header('Content-type: application/json; charset=utf-8');
		sendCCResponse(json_encode($response));
	}

	if (USE_COMET == 1 && COMET_CHATROOMS == 1) {
		if (!empty($name)) {
			$channel = md5('chatroom_'.$to.KEY_A.KEY_B.KEY_C);
			$comet = new Comet(KEY_A,KEY_B);
			if(method_exists($comet, 'processChannel')){
				$channel = processChannel($channel);
			}
			$localmessageid = !empty($localmessageid)? $localmessageid: ((!empty($_GET['callback'])) ? $_GET['callback'] : '');
			$info = $comet->publish(array(
				'channel' => $channel,
				'message' => array ( "id" => $insertedid, "from" => $name, "fromid"=> $userid, "message" => $styleStart.$message.$styleEnd, "sent" => ($timestamp*1000), "roomid" => $to, "groupid" => $to, 'localmessageid' => $localmessageid)
			));
		}
	}

	if(function_exists('hooks_processGroupMessageAfter')) {
		hooks_processGroupMessageAfter(array('to' => $to, 'message' => $message, 'styleStart' => $styleStart, 'styleEnd' => $styleEnd, 'comet' => $comet, 'channel' => $channel, 'timestamp' => $timestamp));
	}

	if(strpos($message,'@') === 0 && $usebots) {
		checkBotMessage($to, $origmessage, 1);
	}

	$parsedmessage = $message;
	if(strpos($message,'BROADCAST_ENDCALL')!==false || strpos($message,'jqcc.ccbroadcast.join')!==false){
		include_once (dirname(__FILE__).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."broadcast".DIRECTORY_SEPARATOR."lang.php");
		global $broadcast_language;
		if(strpos($message,'BROADCAST_ENDCALL')!==false){ $parsedmessage = $broadcast_language[24]; }		//This broadcast has ended
		if(strpos($message,'jqcc.ccbroadcast.join')!==false){ $parsedmessage = $broadcast_language[17]; }	//has started a video broadcast.
    }elseif(strpos($message,'jqcc.ccavchat.join')!==false){
    	include_once (dirname(__FILE__).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."avchat".DIRECTORY_SEPARATOR."lang.php");
    	global $avchat_language;
        $parsedmessage = $avchat_language[19];			//has started a video conversation.
    }elseif(strpos($message,'jqcc.ccaudiochat.join')!==false){
    	include_once (dirname(__FILE__).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."audiochat".DIRECTORY_SEPARATOR."lang.php");
    	global $audiochat_language;
        $parsedmessage = $audiochat_language[19];			//has started a audio conversation.
    }elseif(strpos($message, 'CC^CONTROL_{"type":"plugins","name":"stickers","method":"sendSticker"')!==false){
    	include_once (dirname(__FILE__).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."stickers".DIRECTORY_SEPARATOR."lang.php");
    	global $stickers_language;
        $parsedmessage = $stickers_language[2];				//has sent a sticker.
    }elseif(strpos($message, 'CC^CONTROL_kicked_')!==false || strpos($message, 'CC^CONTROL_banned_')!==false){
    	include_once (dirname(__FILE__).DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR."chatrooms".DIRECTORY_SEPARATOR."lang.php");
    	global $chatrooms_language;
        if(strpos($message,'CC^CONTROL_kicked_')!==false) { $parsedmessage = $chatrooms_language[36]; }				//You have been kicked from this chatroom.
        if(strpos($message,'CC^CONTROL_banned_')!==false) { $parsedmessage = $chatrooms_language[37]; }	//You have been banned from chatroom
    }elseif(strpos($message,'jqcc.ccwhiteboard.accept')!==false){
    	include_once (dirname(__FILE__).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."whiteboard".DIRECTORY_SEPARATOR."lang.php");
    	global $whiteboard_language;
        $parsedmessage = $whiteboard_language[7];						//has shared a whiteboard.
    }elseif(strpos($message,'jqcc.ccwriteboard.accept')!==false){
    	include_once (dirname(__FILE__).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."writeboard".DIRECTORY_SEPARATOR."lang.php");
    	global $writeboard_language;
        $parsedmessage = $writeboard_language[2];						//has shared a writeboard.
    }elseif(strpos($message,'jqcc.ccscreenshare.accept')!==false){
    	include_once (dirname(__FILE__).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."screenshare".DIRECTORY_SEPARATOR."lang.php");
    	global $screenshare_language;
        $parsedmessage = $screenshare_language[2];				//has shared his/her screen with you.
    }elseif(strpos($message,'/writable/handwrite/uploads/')!==false){
    	include_once (dirname(__FILE__).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."handwrite".DIRECTORY_SEPARATOR."lang.php");
    	global $handwrite_language;
        $parsedmessage = $handwrite_language[1];		//has successfully sent a handwritten message
    }elseif(strpos($message, 'plugins/filetransfer/download.php')!==false){
    	include_once (dirname(__FILE__).DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."filetransfer".DIRECTORY_SEPARATOR."lang.php");
    	global $filetransfer_language;
        $parsedmessage = $filetransfer_language[9];								//has shared a file
    }
    if($messagetype != 'botresponse'){
		$response['push'] = pushMobileNotification($to,$insertedid,$parsedmessage,'1',0,$timestamp);
	}

	if(DEV_MODE==1 && $notsilent == 1){
		header('Content-type: application/json; charset=utf-8');
		sendCCResponse(json_encode($response));
	}

	$query = sql_query('updateGroupActivity',array('lastactivity'=>$timestamp, 'id'=>$to));

	if($notsilent == 0) {
		return $response;
	}
}

function sendAnnouncement($to,$message) {
	global $userid;

	if (!empty($to) && isset($message)) {
		$query = sql_query('insertAnnouncement',array('announcement'=>$message, 'time'=>getTimeStamp(), 'to'=>$to));

		if (defined('DEV_MODE') && DEV_MODE == '1') { echo sql_error($GLOBALS['dbh']); }
	}
}
function getPrevMessages($id){
	global $messages;
	global $userid;
	global $chromeReorderFix;
	global $prependLimit;

	if(!empty($_SESSION['cometchat']['cometchat_user_'.$id.'_clear'])){
		return;
	}

	$prelimit = bigintval($prependLimit);
	$messages = array();
	$condition = '';
	if(!empty($_REQUEST['lastid'])){
		$condition = sql_getQuery('getPrevMessages_condition', array('id'=>$_REQUEST['lastid']));
	}

	$query = sql_query('getPrevMessages',array('from'=>$userid, 'to'=>$id, 'fromid'=>$id, 'toid'=>$userid, 'condition'=>$condition, 'prelimit'=>$prelimit));
	if (defined('DEV_MODE') && DEV_MODE == '1') { echo sql_error($GLOBALS['dbh']); }

	while ($chat = sql_fetch_assoc($query)) {
		$self = 0;
		$old = 0;
		if ($chat['from'] == $userid) {
			$chat['from'] = $chat['to'];
			$self = 1;
			$old = 1;
		}

		if ($chat['read'] == 1) {
			$old = 1;
		}

		$messages[$chromeReorderFix.$chat['id']] = array('id' => $chat['id'], 'from' => $chat['from'], 'message' => $chat['message'], 'self' => $self, 'old' => $old, 'sent' => ($chat['sent']), 'direction' => $chat['direction']);
	}
}

function cmp($a, $b) {
	return $a['id'] - $b['id'];
}

function getChatboxData($id) {
	global $messages;
	global $userid;
	global $chromeReorderFix;
	global $prependLimit;
	$limit   = 10;
	if(empty($_REQUEST['prepend'])){
		if(USE_COMET == 1 && !empty($id)) {
			if(!empty($_SESSION['cometchat']['cometmessagesafter'])) {
				$prelimit = ' limit '.intval($limit);
				if(!empty($_REQUEST['callbackfn']) && $_REQUEST['callbackfn'] == 'mobileapp'){
					$prelimit = ' limit 10';
				}
				if (!empty($_SESSION['cometchat']['cometchat_user_'.$id])) {
					$messages = array_merge($messages,$_SESSION['cometchat']['cometchat_user_'.$id]);
				}
				$moremessages = array();
				$messagesafter = $_SESSION['cometchat']['cometmessagesafter'];
				if (!empty($_SESSION['cometchat']['cometchat_user_'.$id.'_clear']) && $_SESSION['cometchat']['cometchat_user_'.$id.'_clear']['timestamp'] > $_SESSION['cometchat']['cometmessagesafter']) {
					$messagesafter = $_SESSION['cometchat']['cometchat_user_'.$id.'_clear']['timestamp'];
				}
				$query = sql_query('getChatboxData',array('from'=>$userid, 'to'=>$id, 'fromid'=>$id, 'toid'=>$userid, 'prelimit'=>$prelimit));
				while($message = sql_fetch_assoc($query)) {
					$self = 0;
					if ($message['from'] == $userid) {
						$message['from'] = $message['to'];
						$self = 1;
					}

					$moremessages[$chromeReorderFix.$message['id']] = array("id" => $message['id'], "from" => $message['from'], "message" => $message['message'], "self" => $self, "old" => 1, 'sent' => ($message['sent']), 'direction' => $message['direction']);
				}
				if(!empty($id) && empty($_SESSION['cometchat']['cometchat_user_'.$id])){
					getPrevMessages($id);
				}
				$messages = array_merge($messages,$moremessages);
				uksort($messages,'compareid');
			}else{
				if (!empty($id) && !empty($_SESSION['cometchat']['cometchat_user_'.$id])) {
					$messages = array_merge($messages,$_SESSION['cometchat']['cometchat_user_'.$id]);
				}
			}
		} else {
			if (!empty($id) && !empty($_SESSION['cometchat']['cometchat_user_'.$id])) {
				$messages = array_replace($messages,$_SESSION['cometchat']['cometchat_user_'.$id]);
			}
			if(!empty($id) && empty($_SESSION['cometchat']['cometchat_user_'.$id])){
				getPrevMessages($id);
				$messages = array_reverse($messages);
			}
		}
	} else {
		$prelimit = intval($prependLimit);
		$messages = array();
		$prependcondition = '';
		if($_REQUEST['prepend'] != '-1'){
			$prepend = bigintval($_REQUEST['prepend']);
			$prependcondition = sql_getQuery('getChatboxData_prependcondition',array('id'=>$prepend));
		}
		$query = sql_query('getChatboxData_prepend',array('from'=>$userid, 'to'=>$id, 'fromid'=>$id, 'toid'=>$userid, 'prepend'=>$prependcondition, 'prelimit'=>$prelimit));
		if (defined('DEV_MODE') && DEV_MODE == '1') { echo sql_error($GLOBALS['dbh']); }
		while ($chat = sql_fetch_assoc($query)) {
			$self = 0;
			$old = 0;
			if ($chat['from'] == $userid) {
				$chat['from'] = $chat['to'];
				$self = 1;
				$old = 1;
			}
			if ($chat['read'] == 1) {
				$old = 1;
			}

			$messages[$chromeReorderFix.$chat['id']] = array('id' => $chat['id'], 'from' => $chat['from'], 'message' => $chat['message'], 'self' => $self, 'old' => $old, 'sent' => ($chat['sent']), 'direction' => $chat['direction']);
		}
		$messages = array_reverse($messages);
	}

	uksort($messages,'reversecompareid');
	$messageIndex = 1;
	$limitedmessages = array();
	foreach ($messages as $key => $value) {
		if($messageIndex>$limit){
			break;
		}
		$limitedmessages[$key] = $value;
		$messageIndex++;
	}
	$messages = $limitedmessages;
	uksort($messages,'compareid');
}

function getChatroomData($chatroomid, $prelimit = 0, $lastMessages = 0) {
	global $guestsMode, $crguestsMode, $guestnamePrefix;
	global $language;
	global $userid;
	global $cookiePrefix;
	global $plugins;
	global $trayicon;

	$usertable = TABLE_PREFIX.DB_USERTABLE;
	$usertable_username = DB_USERTABLE_NAME;
	$usertable_userid = DB_USERTABLE_USERID;
	$messages = array();
	$moremessages = array();
	$guestpart = '';
	$prependCondition = '';
	$limitClause = sql_getQuery('getChatroomData_limitclause',array('lastMessages'=>$lastMessages));

	if(empty($prelimit) && empty($lastMessages)) {
		if (!empty($_SESSION['cometchat']['cometchat_chatroom_'.$chatroomid])) {
			$moremessages = $moremessages + $_SESSION['cometchat']['cometchat_chatroom_'.$chatroomid];
		}
		$messages = $messages + $moremessages;
		krsort($messages);
		return $messages;
	} else {
		if($prelimit > 0){
			$prelimit = bigintval($prelimit);
			$prependCondition = sql_getQuery('getChatroomData_prependcondition',array('id'=>$prelimit));
		}

		if ($guestsMode && $crguestsMode) {
			$guestpart = sql_getQuery('getChatroomData_guestpart',array('guestnamePrefix'=>$guestnamePrefix, 'chatroomid'=>$chatroomid, 'prependCondition'=>$prependCondition));
		}

		$query = sql_query('getChatroomData',array('chatroomid'=>$chatroomid, 'prependCondition'=>$prependCondition, 'guestpart'=>$guestpart, 'limitClause'=>$limitClause, 'usertable'=>$usertable, 'usertable_username'=>$usertable_username, 'usertable_userid'=>$usertable_userid));
		if (defined('DEV_MODE') && DEV_MODE == '1') { echo sql_error($GLOBALS['dbh']); }
		while ($chat = sql_fetch_assoc($query)) {
			if (function_exists('processName')) {
				$chat['from'] = processName($chat['from']);
			}

			if ($lastMessages == 0) {
				$chat['message'] = '';
			}

			if ($userid == $chat['userid']) {
				$chat['from'] = $language[10];

			} else {
				if(!empty($_REQUEST['callbackfn']) && $_REQUEST['callbackfn']=='mobileapp'){
					if (in_array('translate',$trayicon) && !empty($_COOKIE[$cookiePrefix.'rttlang']) && !(strpos($chat['message'],"CC^CONTROL_")>-1)) {
						$translated = text_translate($chat['message'],'',$_COOKIE[$cookiePrefix.'rttlang']);
						if ($translated != '') {
							$chat['message'] = strip_tags($translated).' <span class="untranslatedtext">('.$chat['message'].')</span>';
						}
					}
				}
			}
			$messages[$chat['id']] = array('id' => $chat['id'], 'from' => $chat['from'],'fromid' => $chat['fromid'],'chatroomid' => $chatroomid, 'message' => $chat['message'],'sent' => ($chat['sent']));
		}
	}

	if(!empty($_REQUEST['action']) && $_REQUEST['action']=='updateChatroomMessages'){
		header('Content-type: application/json; charset=utf-8');
		if (!empty($_GET['callback'])) {
			echo $_GET['callback'].'('.json_encode($messages).')';
		} else {
			echo json_encode($messages);
		}
		exit;
	}
	return $messages;
}

function socialLogin($social_details) {
	$userid = 0;
	if(checkAuthMode('social')) {
		if(empty($social_details->firstName)){
			$social_details->firstName = $social_details->displayName;
		}
		$result = sql_query('checkSocialLogin',array('network_name'=> $social_details->network_name, 'identifier'=> $social_details->identifier, 'db_usertable_username'=> DB_USERTABLE_USERNAME));
	    if($row = sql_fetch_assoc($result)){
	        sql_query('updateSocialLogin',array('firstName'=> $social_details->firstName, 'photoURL'=> $social_details->photoURL, 'profileURL'=> $social_details->profileURL, 'network_name'=> $social_details->network_name, 'identifier'=> $social_details->identifier, 'db_usertable_username'=> DB_USERTABLE_USERNAME));
	        if(!empty($row[DB_USERTABLE_USERID])){
	            $userid = $row[DB_USERTABLE_USERID];
	        }
	    }else{
	        sql_query('insertSocialLogin',array('network_name'=> $social_details->network_name, 'identifier'=> $social_details->identifier, 'firstName'=> $social_details->firstName, 'photoURL'=> $social_details->photoURL, 'profileURL'=> $social_details->profileURL, 'groupfield'=> ucfirst($social_details->network_name), 'db_usertable_username'=> DB_USERTABLE_USERNAME, 'db_groupfield'=>DB_GROUPFIELD));
	        $userid = sql_insert_id(DB_USERTABLE);
	    }
	    $_SESSION['cometchat']['userid'] = $userid;
	    $_SESSION['cometchat']['ccauth'] = '1';
	} else if (function_exists('hooks_social_login')){
		$userid = hooks_social_login($social_details);
	}
    return $userid;
}

function compareid($a, $b) { return strnatcmp($a, $b); }
function reversecompareid($a, $b){ return strnatcmp($b, $a); }

function text_translate($text, $from = 'en', $to = 'en') {
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'realtimetranslate'.DIRECTORY_SEPARATOR.'translate.php');
	return translate_text($text,$from,$to);
}

function unescapeUTF8EscapeSeq($str) {
	return preg_replace_callback("/\\\u([0-9a-f]{4})/i", create_function('$matches', 'return bin2utf8(hexdec($matches[1]));'), $str);
}

function bin2utf8($bin) {
	if ($bin <= 0x7F) {
		return chr($bin);
	} elseif ($bin >= 0x80 && $bin <= 0x7FF) {
		return pack("C*", 0xC0 | $bin >> 6, 0x80 | $bin & 0x3F);
	} else if ($bin >= 0x800 && $bin <= 0xFFF) {
		return pack("C*", 0xE0 | $bin >> 11, 0x80 | $bin >> 6 & 0x3F, 0x80 | $bin & 0x3F);
	} else if ($bin >= 0x10000 && $bin <= 0x10FFFF) {
		return pack("C*", 0xE0 | $bin >> 17, 0x80 | $bin >> 12 & 0x3F, 0x80 | $bin >> 6& 0x3F, 0x80 | $bin & 0x3F);
	}
}

function checkcURL($http = 0, $url = '', $params = '', $return = 0, $cookiefile = '') {
	if (!function_exists('curl_init')) {
		return false;
	}
	if (empty($url)) {
		if ($http == 0) {
			$url = 'http://www.microsoft.com';
		} else {
			$url = 'https://www.microsoft.com';
		}
	}
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_URL, $url);
	if (!empty($cookiefile)) {
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiefile);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiefile);
	}
	if ($return == 1) {
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$params);
	}

	$data = curl_exec($ch);
	curl_close($ch);
	if ($return == 1) {
		return $data;
	}
	if (empty($data)) {
		return false;
	}
	return true;
}

function setCache($key,$contents,$timeout = 60) {
	if ((defined('MEMCACHE') && MEMCACHE == 0) || empty($contents) || empty($key)) {
		return false;
	}
	removeCache($key);
	if(!empty($GLOBALS['cookiePrefix'])){
		$key.=$GLOBALS['cookiePrefix'];
	}
	if(!empty($GLOBALS['chromeReorderFix'])){
		$key.=$GLOBALS['chromeReorderFix'];
	}
	$GLOBALS['memcache']->set($key,$contents,$timeout);
}

function getCache($key) {
	if ((defined('MEMCACHE') && MEMCACHE == 0) || empty($key)) {
		return;
	}
	if(!empty($GLOBALS['cookiePrefix'])){
		$key.=$GLOBALS['cookiePrefix'];
	}
	if(!empty($GLOBALS['chromeReorderFix'])){
		$key.=$GLOBALS['chromeReorderFix'];
	}
	return $GLOBALS['memcache']->get($key);
}

function removeCache($key) {
	if ((defined('MEMCACHE') && MEMCACHE == 0) || empty($key)) {
		return;
	}
	if(!empty($GLOBALS['cookiePrefix'])){
		$key.=$GLOBALS['cookiePrefix'];
	}
	$GLOBALS['memcache']->delete($key.'_');
	$GLOBALS['memcache']->delete($key);
	if(!empty($_SESSION['cometchat']['memcache'][$key]) || !empty($_SESSION['cometchat']) || !empty($_SESSION['cometchat']['memcache'])) {
		unset($_SESSION['cometchat']['memcache'][$key]);
	}
}

function getChatroomDetails($id=0) {
	$response =array();
	if(!empty($_GET['action']) && $_GET['action'] == 'getChatroomDetails'){
		$id = $_REQUEST['id'];
	}
	$query = sql_query('getChatroomDetails',array('id'=>$id));
	if (defined('DEV_MODE') && DEV_MODE == '1') { echo sql_error($GLOBALS['dbh']); }
	if($chatroom = sql_fetch_assoc($query)){
		$response = array('id' => $chatroom['id'], 'name' => $chatroom['name'], 'type' => $chatroom['type'], 'password' => $chatroom['password'], 'createdby' => $chatroom['createdby'], 'invitedusers' => $chatroom['invitedusers']);
		$_SESSION['cometchat']['chatrooms']['_'.$id]=$response;
	}
	if(!empty($_GET['action']) && $_GET['action'] == 'getChatroomDetails'){
		if (!empty($_GET['callback'])) {
			header('Content-type: application/json; charset=utf-8');
			echo $_GET['callback'].'('.json_encode($response).')';
		} else {
			echo json_encode($response);
		}
		exit;
	}else{
		return $response;
	}
}

function pushMobileNotification($to,$insertedid,$message,$isChatroom = '0',$isWRTC = '0',$sent = '0'){
	if(empty($insertedid)){
		return array('error'=>'Empty inserted id');
	}
	global $firebaseauthserverkey;
	global $app_title;
	if(strpos($message, 'CC^CONTROL_deletemessage_') !== false || strpos($message, 'CC^CONTROL_deletedchatroom_') !== false) {
		return;
	}
	if(empty($_SESSION['cometchat'])||empty($_SESSION['cometchat']['user'])||empty($_SESSION['cometchat']['user']['n'])){
		getStatus();
	}
	if(empty($sent)){
		$sent = $insertedid;
	}
	$emojiUTF8= include_once (dirname(__FILE__).DIRECTORY_SEPARATOR."extensions".DIRECTORY_SEPARATOR."mobileapp".DIRECTORY_SEPARATOR."emoji_notification.php");
	if(strpos($message,'cometchat_smiley')!==false){
		preg_match_all('/<img[^>]+\>/i',$message,$matches);

		for($i=0;$i<sizeof($matches[0]);$i++){
			$msgpart = (explode('/writable/images/smileys/',$matches[0][$i]));
			$imagenamearr = explode('"',$msgpart[1]);
			$imagename = $imagenamearr[0];
			$smileynamearr = explode('.',$imagename);
			$smileyname = $smileynamearr[0];
			if(!empty($imagename)&&!empty($emojiUTF8[$imagename])){
				$message = str_replace($matches[0][$i],$emojiUTF8[$imagename],$message);
			}else{
				$message = str_replace($matches[0][$i],':'.$smileyname.':',$message);
			}
		}

	}

	global $userid;
	global $channelprefix;
	if($isChatroom === '0'){
		$rawMessage = array("name" => $_SESSION['cometchat']['user']['n'], "fid"=> $userid, "m" => $message, "sent" => $sent);
		if(strlen($insertedid) < 13) {
			$rawMessage['id'] = $insertedid;
		}
		$channel = md5($channelprefix."USER_".$to.BASE_URL);
	} else {
		$roomname = '';
		if(empty($_SESSION['cometchat']) || empty($_SESSION['cometchat']['chatrooms']) || empty($_SESSION['cometchat']['chatrooms']['_'.$to])){
			$room = getChatroomDetails($to);
			if(!empty($room)&&!empty($room['name'])){
				$roomname = $room['name'];
			}
		}
		if(!empty($_SESSION['cometchat']) && !empty($_SESSION['cometchat']['chatrooms']) && !empty($_SESSION['cometchat']['chatrooms']['_'.$to]) && !empty($_SESSION['cometchat']['chatrooms']['_'.$to]['name'])){
			$roomname = $_SESSION['cometchat']['chatrooms']['_'.$to]['name'];
		}
		$parsedmessage = $_SESSION['cometchat']['user']['n']."@".$roomname.": ".$message;
		if (strpos($message, "has shared a file") !== false) {
			$parsedmessage = $_SESSION['cometchat']['user']['n']."@".$roomname.": "."has shared a file";
		}

		$rawMessage = array( "id" => $insertedid, "from" => $_SESSION['cometchat']['user']['n'], "fid"=> $userid, "m" => sanitize($parsedmessage), "sent" => $sent, "cid" => $to);
		$channel = md5($channelprefix."CHATROOM_".$to.BASE_URL);
	}
	pushToMobileDevice($channel, $rawMessage, $isChatroom, 0, $isWRTC);
}
function pushToMobileDevice($channel, $rawMessage, $isChatroom, $isAnnouncement, $isWRTC){
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'extensions'.DIRECTORY_SEPARATOR.'mobileapp'.DIRECTORY_SEPARATOR.'PushNotification.php');
	$pushnotifier = new PushNotification();
	return $pushnotifier->sendNotification($channel, $rawMessage, $isChatroom, $isAnnouncement, $isWRTC);
}
function getPlatformSuffix($suffix) {
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'extensions'.DIRECTORY_SEPARATOR.'mobileapp'.DIRECTORY_SEPARATOR.'PushNotification.php');
	$pushnotifier = new PushNotification();
	return $pushnotifier->getPlatformSuffix($suffix);
}
function incrementCallback(){
	if(!empty($_REQUEST['callback'])){
		$explodedCallback = explode('_',$_REQUEST['callback']);
		$explodedCallback[1].='_';
		$_REQUEST['callback'] = implode('_', $explodedCallback);
	}
}
function decrementCallback(){
	if(!empty($_REQUEST['callback'])){
		$explodedCallback = explode('_',$_REQUEST['callback']);
		array_pop($explodedCallback);
		$_REQUEST['callback'] = implode('_', $explodedCallback);
	}
}

function sendCCResponse($response){
	ob_end_clean();
	header("Connection: close\r\n");
	header("Content-Encoding: none\r\n");
	ignore_user_abort(true); // optional
	ob_start();
	if (!empty($_GET['callback'])) {
		echo $_GET['callback'].'('.$response.')';
	} else {
		echo $response;
	}
	$size = ob_get_length();
	header("Content-Length: $size");
	ob_end_flush();     // Strange behaviour, will not work
	flush();            // Unless both are called !
	ob_end_clean();
	session_write_close();
}

function bigintval($value) {
  $value = trim($value);
  if (ctype_digit($value)) {
    return $value;
  }
  $value = preg_replace("/[^0-9](.*)$/", '', $value);
  if (ctype_digit($value)) {
    return $value;
  }
  return 0;
}

function updateLastActivity($userid) {
	$sql = sql_getQuery('updateLastActivity',array('userid'=>$userid, 'timestamp'=>getTimeStamp()));
	return $sql;
}

function setLastseensettings($message) {

	global $userid;
	if($message == 'true'){
		$message = 1;
	} else{
		$message = 0;
	}
	$query = sql_query('setLastseensettings',array('userid'=>$userid, 'message'=>$message));
	if (defined('DEV_MODE') && DEV_MODE == '1') { echo sql_error($GLOBALS['dbh']); }

	if (function_exists('hooks_activityupdate')) {
		hooks_activityupdate($userid,$message);
	}

}

function setReadReceiptsettings($message){
	global $userid;
	if($message == 'true'){
		$message = 1;
	} else{
		$message = 0;
	}
	$query = sql_query('setReadReceiptsettings',array('userid'=>$userid, 'message'=>$message));
	if (defined('DEV_MODE') && DEV_MODE == '1') { echo sql_error($GLOBALS['dbh']); }
}

function getStatus() {
	global $response;
	global $userid;
	global $status;
	global $startOffline;
	global $processFurther;
	global $channelprefix;
	global $language;
	global $cookiePrefix;
	global $announcementpushchannel;
	global $bannedUserIDs;
	global $pushplatformsuffix;
	global $firstguestID;

    if ($userid > $firstguestID) {
        $sql = getGuestDetails($userid);
    } else {
        $sql = getUserDetails($userid);
    }

 	$query = sql_query($sql,array(),1);
	if (defined('DEV_MODE') && DEV_MODE == '1') { echo sql_error($GLOBALS['dbh']); }
	if(sql_num_rows($query) > 0) {
		$chat = sql_fetch_assoc($query);
		if (!empty($_REQUEST['callbackfn'])) {
			$_SESSION['cometchat']['startoffline'] = 1;
		}
		if ($startOffline == 1 && empty($_SESSION['cometchat']['startoffline'])) {
			$_SESSION['cometchat']['startoffline'] = 1;
			$chat['status'] = 'offline';
			setStatus('offline');
			$_SESSION['cometchat']['cometchat_sessionvars']['buddylist'] = 0;
			$processFurther = 0;
		} else {
			if (empty($chat['status'])) {
				$chat['status'] = 'available';
			} else {
				if ($chat['status'] == 'away') {
					/*$chat['status'] = 'available';
					setStatus('available');*/
				}

				if ($chat['status'] == 'offline') {
					$processFurther = 0;
					$_SESSION['cometchat']['cometchat_sessionvars']['buddylist'] = 0;
				}
			}
		}

		if (empty($chat['message'])) {
			$chat['message'] = $status[$chat['status']];
		}

		if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR."announcements".DIRECTORY_SEPARATOR."config.php")){
			include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR."announcements".DIRECTORY_SEPARATOR."config.php");
		}

		$chat['message'] = html_entity_decode($chat['message']);

		$ccmobileauth = 0;
		if (!empty($_REQUEST['callbackfn']) && $_REQUEST['callbackfn'] == 'ccmobiletab') {
			$ccmobileauth = md5($_SESSION['basedata'].'cometchat');
		}

		if (empty($chat['ch'])) {
			if( defined('KEY_A') && defined('KEY_B') && defined('KEY_C') ){
				$key = KEY_A.KEY_B.KEY_C;
			}
			$chat['ch'] = md5($chat['userid'].$key);
		}
		if(empty($chat['readreceiptsetting']) || $chat['readreceiptsetting'] == null || $chat['readreceiptsetting'] == "null"){
			$chat['readreceiptsetting'] = 0;
			if(MESSAGE_RECEIPT==1){
				$chat['readreceiptsetting'] = 1;
			}
		}

		if(!empty($_REQUEST['appinfo'])){
			if(gettype($_REQUEST['appinfo']) == 'string'){
				$_REQUEST['appinfo'] = json_decode($_REQUEST['appinfo'],true);
			}
			if(!empty($_REQUEST['appinfo']['os']) && !empty($_REQUEST['appinfo']['os']['n'])){
				$pushplatformsuffix = $_REQUEST['appinfo']['os']['n'];
			}
		}

		if($chat['lastseen'] == null || $chat['lastseen'] == "null"){
			$chat['lastseen'] = '';
		}

		if($chat['lastseensetting'] == null || $chat['lastseensetting'] == "null"){
			$chat['lastseensetting'] = '';
		}

	    $s = array('id' => $chat['userid'], 'n' => $chat['username'], 'l' => fetchLink($chat['link']), 'a' => getAvatar($chat['avatar']), 's' => $chat['status'], 'm' => $chat['message'],'push_channel' => 'C_'.md5($channelprefix."USER_".$userid.BASE_URL).getPlatformSuffix($pushplatformsuffix), 'ccmobileauth' => $ccmobileauth, 'push_an_channel' => $announcementpushchannel.getPlatformSuffix($pushplatformsuffix), 'webrtc_prefix' => $channelprefix, 'ch' => $chat['ch'], 'ls' => $chat['lastseen'], 'lstn' => $chat['lastseensetting'],'rdrs' => $chat['readreceiptsetting']);

	    if(in_array($chat['userid'],$bannedUserIDs)) {
			$s['b'] = 1;
		}
		$response['userstatus'] = $_SESSION['cometchat']['user'] = $s;
	} else if(!checkAuthMode('social')){
		$response['loggedout'] = '1';
		$response['logout_message'] = $language[30];
		setcookie($cookiePrefix.'guest','',time()-3600,'/');
		setcookie($cookiePrefix.'state','',time()-3600,'/');
		unset($_SESSION['cometchat']);
	}
}

function setStatus($message) {
	global $userid;
	$query = sql_query('setStatus',array('userid'=>$userid, 'message'=>$message));
	if (defined('DEV_MODE') && DEV_MODE == '1') {
		echo sql_error($GLOBALS['dbh']);
	}
	if (function_exists('hooks_activityupdate')) {
		hooks_activityupdate($userid,$message);
	}
}

function encryptUserid($userid) {
	$encrypteduserid = $userid;
	if($userid && function_exists('mcrypt_encrypt') && defined('ENCRYPT_USERID') && ENCRYPT_USERID == '1') {
		$key = "";
		if( defined('KEY_A') && defined('KEY_B') && defined('KEY_C') ){
			$key = KEY_A.KEY_B.KEY_C;
		}
		$encrypteduserid = rawurlencode(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $userid, MCRYPT_MODE_CBC, md5(md5($key)))));
	}
	return $encrypteduserid;
}

function decryptUserid($encrypteduserid) {
	$userid = 0;
	if (!empty($encrypteduserid)) {
		if (function_exists('mcrypt_encrypt') && defined('ENCRYPT_USERID') && ENCRYPT_USERID == '1') {
			$key = "";
			if( defined('KEY_A') && defined('KEY_B') && defined('KEY_C') ){
				$key = KEY_A.KEY_B.KEY_C;
			}
			$uid = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode(rawurldecode($encrypteduserid)), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
			if (intval($uid) > 0) {
				$userid = $uid;
			}
		} else {
			$userid = $encrypteduserid;
		}
	}
	return $userid;
}

function getUserID() {
	global $integration;
	return $integration->getUserID();
}

function chatLogin($userName,$userPass) {
	global $integration;
	return $integration->chatLogin($userName,$userPass);
}

function getFriendsList($userid,$time) {
	global $integration;
	return $integration->getFriendsList($userid,$time);
}

function getFriendsIds($userid) {
	global $integration;
	return $integration->getFriendsIds($userid);
}

function getUserDetails($userid) {
	global $integration;
	return $integration->getUserDetails($userid);
}

function getActivechatboxdetails($userids) {
	global $integration;
	return $integration->getActivechatboxdetails($userids);
}

function fetchLink($link) {
	global $integration;
	return $integration->fetchLink($link);
}

function getAvatar($image) {
	global $integration;
	return $integration->getAvatar($image);
}

function getTimeStamp() {
	global $integration;
	return $integration->getTimeStamp();
}

function processTime($time) {
	global $integration;
	return $integration->processTime($time);
}

function processName($name) {
	global $integration;
	if(method_exists($integration, 'processName')){
		return $GLOBALS['integration']->processName($name);
	}
	return $name;
}

function getRoleId($userid) {
	global $integration;
	if(method_exists($integration, 'getRoleId') && defined('ROLE_BASE_ACCESS') && ROLE_BASE_ACCESS == 1){
		return $GLOBALS['integration']->getRoleId($userid);
	}
	return '';
}

function getRolesDetails($roleid = '') {
	global $integration;
	if(method_exists($integration, 'getRolesDetails') && defined('ROLE_BASE_ACCESS') && ROLE_BASE_ACCESS == 1){
		return $GLOBALS['integration']->getRolesDetails($roleid);
	}
	return '';
}

function checkMembershipAccess($feature, $type) {
	if(defined('ROLE_BASE_ACCESS') && ROLE_BASE_ACCESS == 1){
		global $userid, $language;
		$getRoleId = getRoleId($userid);
		global ${$getRoleId.'_'.$type};
		$memberFeature = ${$getRoleId.'_'.$type};
		if (in_array($feature, $memberFeature)) {
			return true;
		} else{
			echo $language['membership_msg'];
			return false;
		}
	}
	return true;
}

function getcmsSettings(){
	global $integration;
	if(method_exists($integration, 'getcmsSettings')){
		return $GLOBALS['integration']->getcmsSettings();
	}
	return false;
}

function getDockedCode(){
	global $integration;
	if(method_exists($integration, 'getDockedCode')){
		return $GLOBALS['integration']->getDockedCode();
	}
	return false;
}

function getCometChatEmbedCode($param = array()){
	global $integration;
	if(method_exists($integration, 'getCometChatEmbedCode')){
		return $GLOBALS['integration']->getCometChatEmbedCode($param);
	}
	return false;
}

/* HOOKS */

function hooks_message($userid,$to,$unsanitizedmessage,$dir) {
	global $integration;
	if(method_exists($integration, 'hooks_message')){
		return $integration->hooks_message($userid,$to,$unsanitizedmessage,$dir);
	}
}

function hooks_forcefriends() {
	global $integration;
	if(method_exists($integration, 'hooks_forcefriends')){
		return $integration->hooks_forcefriends();
	}
}

function hooks_updateLastActivity($userid) {
	global $integration;
	if(method_exists($integration, 'hooks_updateLastActivity')){
		return $integration->hooks_updateLastActivity($userid);
	}
}

function hooks_statusupdate($userid,$statusmessage) {
	global $integration;
	if(method_exists($integration, 'hooks_statusupdate')){
		return $integration->hooks_statusupdate($userid,$statusmessage);
	}
}

function hooks_activityupdate($userid,$status) {
	global $integration;
	if(method_exists($integration, 'hooks_activityupdate')){
		return $integration->hooks_activityupdate($userid,$status);
	}
}

function cc_curl_call($url, $postdata) {
	$result = false;
	$cc_path = "";
	$protocol = isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] != 'off') ? 'https' : 'http';
	$parsed_url = parse_url(BASE_URL);
	if(empty($parse_url['scheme'])){
		$parsed_url['scheme'] = $protocol;
	}
	if(empty($parsed_url['host'])){
		$parsed_url['host'] = !empty($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : $_SERVER['HOST'];
	}
	$cc_path = $parsed_url['scheme']."://".$parsed_url['host'].$parsed_url['path'];
	if(!empty($url)) {
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 2);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_REFERER, $cc_path);
		if(!empty($postdata)){
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
		}
		$result = curl_exec($ch);
		curl_close($ch);
	}
	return $result;
}

function getsetting($setting_key) {
	global $dbh;
	$sql = ("select value from cometchat_settings where setting_key = '".sql_real_escape_string($setting_key)."'");
	$query = sql_query($sql,array(),1);
	if ($query) {
		$result = sql_fetch_array($query);
	} else {
		return '';
	}

	if (!empty($result['value'])) {
		return $result['value'];
	} else {
		return '';
	}
}

function fetchURL($url,$fields) {

	$fields_string = '';

	foreach($fields as $key=>$value) { $fields_string .= $key.'='.($value).'&'; }
	rtrim($fields_string, '&');

	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL, $url);
	curl_setopt($ch,CURLOPT_POST, count($fields));
	curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
	$result = curl_exec($ch);
	curl_close($ch);

    return $result;
}

function checkplan($type,$name = '') {
    /*
    * Checks Cloud and selfhosted plans.
    * Params: $type =  type of feature (eg:plugins,extensions,modules),$name = name of the feature
    * Returns/Results 1 if plan exist 0 if it does not.
    */
    global $dbh, $planInfo, $api_response, $client,$licensekey,$p_,$planId;

    $o_plugins = array('audiochat' => 4,
    					'avchat' => 4,
    					'block' => 1,
    					'broadcast' => 4,
    					'report' => 1,
    					'save' => 2,
    					'screenshare' => 4,
    					'transliterate' => 2,
    					'whiteboard' => 3,
    					'writeboard' => 3,
    	);
    if(!empty($client) && empty($planId)){
        $plan = getsetting('plan');
        if ($planInfo[$type][$plan] == 1 || (is_array($planInfo[$type][$plan]) && in_array($name,$planInfo[$type][$plan]))) {
            return 1;
        }
    }
    if(!checkLicenseVersion()){
    	if((!empty($o_plugins[$name]) && $p_>=$o_plugins[$name]) || empty($o_plugins[$name]))
    		return 1;
    }
    if(checkLicenseVersion() && !empty($api_response['plan'][$type]) && is_array($api_response['plan'][$type]) && in_array($name,$api_response['plan'][$type])){
        return 1;
    } else {
        return 0;
    }
}

function getCometServiceVersion(){
	$version = 0;
	if(defined('KEY_B')){
		if(KEY_B=='demo' || strpos(KEY_B,'sub-c-')===0 ){
			$version = 1;
		}elseif(strpos(KEY_B,'cs2')===0){
			$version = 2;
		}
	}
	return $version;
}

function file_get_contents_curl_core($url,$path){
	set_time_limit(0);
	$fp = fopen($path, 'w+');
	$ch = curl_init(str_replace(" ","%20",$url));
	curl_setopt($ch, CURLOPT_TIMEOUT, 50);
	curl_setopt($ch, CURLOPT_FILE, $fp);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	$data = curl_exec($ch);
	curl_close($ch);
	fclose($fp);
	return $data;
}

function setBaseUrl() {
	$baseurl = '/cometchat/';
	if (!empty($_SERVER['DOCUMENT_ROOT']) && !empty($_SERVER['SCRIPT_FILENAME'])) {
		$baseurl = preg_replace("/admin\/index.php/i",'',str_replace($_SERVER['DOCUMENT_ROOT'],'',$_SERVER['SCRIPT_FILENAME']));
		$baseurl = preg_replace("/install.php/i",'',$baseurl);
		$baseurl = preg_replace("/cometchatjs.php/i",'',$baseurl);
		$baseurl = preg_replace("/cometchatcss.php/i",'',$baseurl);
		$baseurl = preg_replace("/cometchat_receive.php/i",'',$baseurl);
		$baseurl = preg_replace("/css.php/i",'',$baseurl);
		$baseurl = preg_replace("/js.php/i",'',$baseurl);
	}
	$baseurl = str_replace('\\','/',$baseurl);
	if (!empty($baseurl) && $baseurl[0] != '/') {
		$baseurl = '/'.$baseurl;
	}
	if (!empty($baseurl) && $baseurl[strlen($baseurl)-1] != '/') {
		$baseurl = $baseurl.'/';
	}
	if($baseurl != '/cometchat/'){
	    $query = sql_query('setBaseUrl', array('baseurl'=>$baseurl));
	    if (defined('DEV_MODE') && DEV_MODE == '1') { echo sql_error($GLOBALS['dbh']); }
  	}

}

function log_error($message, $logtype="error") {
	global $client;
	global $papertrail_url;
	global $papertrail_port;
	global $currentversion;
	if(!empty($client) && ($_SERVER['environment'] == 'local' || $_SERVER['environment'] == 'dev' || $_SERVER['environment'] == 'app')&&!empty($papertrail_url)&&!empty($papertrail_port)) {
	$msg = "<22>" . date('M j H:i:s ') . 'cod-'.$_SERVER['environment'] . ' ' . $client . ': ' . $message;
	$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
	socket_sendto($sock, $msg, strlen($msg), 0, $papertrail_url, $papertrail_port);
	socket_close($sock);
	}

	if(empty($client)){
		$msg = "\n " . date('M j H:i:s ') . ': ' . $message;
		$folder = dirname(__FILE__).DIRECTORY_SEPARATOR.'writable/logs';
		if(!is_dir($folder)){
			mkdir($folder, 0777, true);
		}
		error_log($msg, 3, $folder."/".$logtype."_".$currentversion.".log");
	}
}

function error_handler($errno,$errstr, $file, $line) {
	$debug_backtrace = 'print_r(debug_backtrace(),true)';
	if(defined('DEV_MODE')&&DEV_MODE==1){
		$debug_backtrace = print_r(debug_backtrace(),true);
	}
	$errorlog = <<<EOD
		\r\n****************************
		\r\n{$file} [L: {$line}] [Level: {$errno}]
		\r\n{$errstr}
		\r\n{$debug_backtrace}
		\r\n****************************
EOD;
	log_error($errorlog, "error");
}

function checkLicenseVersion(){
	/*
	* Checks New License
	*/
	if(substr($GLOBALS['licensekey'],0,10) == "COMETCHAT-"){
		return true;
	}
	return false;
}

function checkAuthMode($name){
	/*
	* Checks status Authentication mode for specified key
	*/
	if(checkLicenseVersion() && !empty($GLOBALS['authentication']) && in_array($name, $GLOBALS['authentication'])){
		return true;
	}

	if(!checkLicenseVersion() && ((USE_CCAUTH == 1 && $name == 'social') || ($GLOBALS['guestsMode'] == 1 && $name == 'guest'))){
		return true;
	}
	return false;
}

function stripSlashesDeep($value){
	$value = is_array($value) ? array_map('stripSlashesDeep', $value) : stripslashes($value);
	return $value;
}


function showSettingsUI($options = array()){
	$formUI = '';
	if (!empty($options)) {
		foreach ($options as $option => $result) {
			global ${$option};
			if (!empty($result[2])) { /* FOR CONSTANT */
				${$option} = $result[2];
			}
			$formUI .= '<div class="form-group row"><div class="col-md-12"><label class="form-control-label">'.$result[1].'</label>';
			if ($result[0] == 'textbox') {
				$formUI .= '<input class="form-control" name="'.$option.'" value="'.${$option}.'" autocomplete="off" type="text">';
			}
			if ($result[0] == 'display') {
				$formUI .= '<input class="form-control" readonly name="'.$option.'" value="'.${$option}.'" autocomplete="off" type="text">';
			}
			if ($result[0] == 'choice') {
				if (${$option} == 1) {
					$formUI .='<div class=""><label class=""><div style="position:relative;top:4px;"><input style="position: absolute;" name="'.$option.'" value="1" type="radio" checked ></div><span style="padding-left:25px;">Yes</span></label><label class=""><div style="position:relative;top:4px;"><input style="position: absolute;left:8px;" name="'.$option.'" value="0" type="radio"></div><span style="padding-left:36px;">No</span></label></div>';
				} else {
					$formUI .='<div class=""><label class=""><div style="position:relative;top:4px;"><input style="position: absolute;" name="'.$option.'" value="1" type="radio" ></div><span style="padding-left:25px;">Yes</span></label><label class=""><div style="position:relative;top:4px;"><input style="position: absolute;left:8px;" name="'.$option.'" value="0" type="radio" checked></div><span style="padding-left:36px;">No</span></label></div>';
				}
			}
			if ($result[0] == 'dropdown') {
				$formUI .= '<select class="form-control"  name="'.$option.'">';
				foreach ($result[2] as $opt) {
					if ($opt == ${$option}) {
						$formUI .= '<option value="'.$opt.'" selected>'.ucwords($opt);
					} else {
						$formUI .= '<option value="'.$opt.'">'.ucwords($opt);
					}
				}
				$formUI .= '</select>';
			}
			$formUI .= '</div></div>';
		}
	}
	return $formUI;
}
