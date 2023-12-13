<?php

/**
 * Playbooks to use with the facebook service
 *
 */
class FacebookPlaylist
{
	private $_access_token;
	private $_app_id;

	public function __construct($access_token = null, $app_id = null)
	{
		if (empty(FACEBOOKAPPID)) throw new \Exception('Missing FACEBOOKAPPID in ss_environment');
		$this->_app_id = FACEBOOKAPPID;

		$this->_access_token = $access_token;
	}

	public function setAccessToken($access_token)
	{
		$this->_access_token = $access_token;
		return $this;
	}

	public function setAppID($app_id)
	{
		$this->_app_id = $app_id;
	}

	public function createUser(Member $user)
	{
		$result = (new FacebookService($this->_access_token))->retrieveUserDetails(['name', 'first_name', 'last_name', 'email', 'picture']);
		$result = json_decode($result);

		$emailUser = null;

		if (! empty($result->email)) {
			$emailUser = Member::get()->filter('Email', $result->email)->first();
		}

		if ($emailUser) {
			$emailUser->FacebookUserID = $user->FacebookUserID;
			$user->delete();
			$user = $emailUser;
		} else {
			$user->FacebookName = !empty($result->name) ? $result->name : null;
			$user->Email = !empty($result->email) ? $result->email : null;
			$user->FirstName = isset($result->first_name) ? $result->first_name : null;
			$user->Surname = isset($result->last_name) ? $result->last_name : null;
		}

		$user->write();

		return $user;
	}


	public function loginUser()
	{
		$id = $this->checkUser();
		$this->checkApp();

		return $id;
	}

	public function checkUser()
	{
		$body = (new FacebookService($this->_access_token))->retrieveUser();
		$result = json_decode($body);

		if (isset($result->error)) throw new \Exception('Access token not correct', 1005);
		if (empty($result->id) || $result->id === null) throw new \Exception('Unable to login', 1006);
		return $result->id;
	}

	public function checkApp()
	{
		$body = (new FacebookService($this->_access_token))->retrieveAppID();
		$result = json_decode($body);

		if ($result->id != $this->_app_id) throw new \Exception('User or App id does not validate with facebook', 1007);
		return true;
	}
        
        public function getFriends($fbId) {
            $body = (new FacebookService($this->_access_token))->retrieveFriends($fbId);
            $result = json_decode($body, true);
            return $result;
        }

}