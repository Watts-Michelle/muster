<?php

class Authorization
{
    private $server;

    private $user;

    private $userUUID;

    private $userID;

    public function __construct()
    {
        $this->server = new \League\OAuth2\Server\AuthorizationServer;
        $this->server->setSessionStorage(new SessionStorage());
        $this->server->setAccessTokenStorage(new AccessTokenStorage());
        $this->server->setClientStorage(new ClientStorage());
        $this->server->setScopeStorage(new ScopeStorage());
        $this->server->setRefreshTokenStorage(new RefreshTokenStorage);

        $refreshTokenGrant = new \League\OAuth2\Server\Grant\RefreshTokenGrant();
        $refreshTokenGrant->setRefreshTokenTTL(1209600);
        $this->server->addGrantType($refreshTokenGrant);

        $facebookGrant = new FacebookGrant();

        $facebookGrant->setVerifyCredentialsCallback(function ($facebookUserID) {

            $user = Member::get()->filter('FacebookUserID', $facebookUserID)->first();

            if (empty($user)) {
                $user = new Member;
                $user->FacebookUserID = $facebookUserID;
                $user->addFounderBadge();
                $user->write();
                $exists = 0;
            }
            else {
                $exists = 1;
                ConcurrentLoginSecurity::check($user);
                $user->write();
            }

            $this->user = $user;
            $this->userUUID = $user->UUID;
            $this->userID = $user->ID;

            return [
                'exists' => $exists,
                'email' => ! empty($user->Email) ? 1 : 0,
                'user' => $user
            ];
        });

        $this->server->addGrantType($facebookGrant, 'facebook');
        $this->server->setTokenType(new CustomBearer);
    }

    public function login($data)
    {
        $this->server->getRequest()->request->set('grant_type', isset($data['grant_type']) ? $data['grant_type'] : null);
        $this->server->getRequest()->request->set('client_id', isset($data['client_id']) ? $data['client_id'] : null);
        $this->server->getRequest()->request->set('client_secret', isset($data['client_secret']) ? $data['client_secret'] : null);

        if (isset($data['refresh_token'])) $this->server->getRequest()->request->set('refresh_token', $data['refresh_token']);
        if (isset($data['access_token'])) $this->server->getRequest()->request->set('access_token', $data['access_token']);

        $this->server->setAccessTokenTTL(14400);
        $response = $this->server->issueAccessToken();
        $response['user_uid'] = $this->userUUID;
        $response['chat_id'] = $this -> userID;
        $response['status'] = 'success';
        
        if (isset($data['refresh_token'])) {
            $response['exists'] = 1;
        }
        
//        if(LoginCount::get()->filter('MemberID', $this->user->ID)->Count() < 5){
//            date_default_timezone_set('Europe/London');
//            $LoginCount = LoginCount::create();
//            $LoginCount->LoginDate = date("Y-m-d h:i:s");
//            $LoginCount->MemberID = $this->userID;
//            $LoginCount->write();
//            $this->user->LoginCounts()->add($LoginCount);
//        }

        return $response;
    }
}