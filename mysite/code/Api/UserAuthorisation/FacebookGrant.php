<?php

use League\OAuth2\Server\Entity\AccessTokenEntity;
use League\OAuth2\Server\Entity\ClientEntity;
use League\OAuth2\Server\Entity\RefreshTokenEntity;
use League\OAuth2\Server\Entity\SessionEntity;
use League\OAuth2\Server\Event;
use League\OAuth2\Server\Exception;
use League\OAuth2\Server\Util\SecureKey;
use League\OAuth2\Server\Grant\AbstractGrant as AbstractGrant;

class FacebookGrant extends AbstractGrant {

	private $facebook_app_id;

	/**
	 * Grant identifier
	 *
	 * @var string
	 */
	protected $identifier = 'facebook';

	/**
	 * Response type
	 *
	 * @var string
	 */
	protected $responseType;

	/**
	 * Callback to authenticate a user's name and password
	 *
	 * @var callable
	 */
	protected $callback;

	/**
	 * Access token expires in override
	 *
	 * @var int
	 */
	protected $accessTokenTTL;

	/**
	 * Does the user exist already
	 * @var boolean
	 */
	protected $exists;

	public function __construct()
	{
		if (empty(FACEBOOKAPPID)) throw new \Exception('Missing FACEBOOKAPPID in ss_environment');
		$this->facebook_app_id = FACEBOOKAPPID;
	}

	/**
	 * Set the callback to verify a user's username and password
	 *
	 * @param callable $callback The callback function
	 *
	 * @return void
	 */
	public function setVerifyCredentialsCallback(callable $callback)
	{
		$this->callback = $callback;
	}

	/**
	 * Return the callback function
	 *
	 * @return callable
	 *
	 * @throws
	 */
	protected function getVerifyCredentialsCallback()
	{
		if (is_null($this->callback) || !is_callable($this->callback)) {
			throw new Exception\ServerErrorException('Null or non-callable callback set on Facebook grant');
		}

		return $this->callback;
	}

	/**
	 * Complete the password grant
	 *
	 * @return array
	 *
	 * @throws
	 */
	public function completeFlow()
	{
		// Get the required params
		$clientId = $this->server->getRequest()->request->get('client_id', $this->server->getRequest()->getUser());
		if (is_null($clientId)) {
			throw new Exception\InvalidRequestException('client_id');
		}

		$clientSecret = $this->server->getRequest()->request->get('client_secret',
			$this->server->getRequest()->getPassword());
		if (is_null($clientSecret)) {
			throw new Exception\InvalidRequestException('client_secret');
		}

		// Validate client ID and client secret
		$client = $this->server->getClientStorage()->get(
			$clientId,
			$clientSecret,
			null,
			$this->getIdentifier()
		);

		if (($client instanceof ClientEntity) === false) {
			$this->server->getEventEmitter()->emit(new Event\ClientAuthenticationFailedEvent($this->server->getRequest()));
			throw new Exception\InvalidClientException();
		}

		$facebook = new FacebookPlaylist($this->server->getRequest()->request->get('access_token'), $this->facebook_app_id);
		$facebook_id = $facebook->loginUser();

		// Check if user's username and password are correct
		$user_details = call_user_func($this->getVerifyCredentialsCallback(), $facebook_id);

		if ($user_details === false) {
			$this->server->getEventEmitter()->emit(new Event\UserAuthenticationFailedEvent($this->server->getRequest()));
			throw new Exception\InvalidCredentialsException();
		} else if ($user_details['user']->UserStatus === 'Deleted') {
			throw new UserDeletedException();
                }
                
		//if the user did not previously had email then grab some of their details from facebook
		if (! $user_details['email']) {
			$user_details['user'] = $facebook->createUser($user_details['user']);
		}
                
                //if user is newly created and has an email
                
                if (! $user_details['exists'] && $user_details['email']) {  
                            
                    $SiteConfig = SiteConfig::current_site_config();

                    $search = array(
                        "%%USERNAME%%" => $user_details['user']->FirstName,
                    );

                    $body = str_replace(array_keys($search), array_values($search), $SiteConfig->WelcomeEmailContent);

                    $email = new Email();
                    $email
                        ->setFrom($SiteConfig->WelcomeEmailFrom)
                        ->setTo($user_details['user']->Email)
                        ->setSubject($SiteConfig->WelcomeEmailSubject)
                        ->setTemplate('ReportEmail')
                        ->populateTemplate(array(
                            'Content' => $body
                        ));

                    $email->send();
                }  

		// Validate any scopes that are in the request
		$scopeParam = $this->server->getRequest()->request->get('scope', '');
		$scopes = $this->validateScopes($scopeParam, $client);

		// Create a new session
		$session = new SessionEntity($this->server);
		$session->setOwner('user', $user_details['user']->ID);
		$session->associateClient($client);

		// Generate an access token
		$accessToken = new AccessTokenEntity($this->server);
		$accessToken->setId(SecureKey::generate());
		$accessToken->setExpireTime($this->getAccessTokenTTL() + time());

		// Associate scopes with the session and access token
		foreach ($scopes as $scope) {
			$session->associateScope($scope);
		}

		foreach ($session->getScopes() as $scope) {
			$accessToken->associateScope($scope);
		}

		$this->server->getTokenType()->setSession($session);
		$this->server->getTokenType()->setParam('access_token', $accessToken->getId());
		$this->server->getTokenType()->setParam('expires_in', $this->getAccessTokenTTL());
		$this->server->getTokenType()->setParam('exists', $user_details['exists']);

		// Associate a refresh token if set
		if ($this->server->hasGrantType('refresh_token')) {
			$refreshToken = new RefreshTokenEntity($this->server);
			$refreshToken->setId(SecureKey::generate());
			$refreshToken->setExpireTime($this->server->getGrantType('refresh_token')->getRefreshTokenTTL() + time());
			$this->server->getTokenType()->setParam('refresh_token', $refreshToken->getId());
		}

		// Save everything
		$session->save();
		$accessToken->setSession($session);
		$accessToken->save();

		if ($this->server->hasGrantType('refresh_token')) {
			$refreshToken->setAccessToken($accessToken);
			$refreshToken->save();
		}

		return $this->server->getTokenType()->generateResponse();
	}



}


