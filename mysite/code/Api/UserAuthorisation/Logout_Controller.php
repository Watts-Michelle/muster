<?php

class Logout_Controller extends Api_Controller {

    protected $auth = true;

    private static $allowed_actions = array(
        'logout'
    );

    private static $url_handlers = array(
        '' => 'logout'
    );

    /**
     * Delete a single access token to log a user out
     *
     * @return SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function logout(SS_HTTPRequest $request) {
        if (! $request->isDELETE()) $this->handleError(404, 'Request must be DELETE.');

        /** @var OauthAccessToken $token */
        $token = OauthAccessToken::get()->filter(['AccessToken' => $this->authServer->getAccessToken()])->first();

        $sessionStorage = new SessionStorage();
        $sessionStorage->removeAccessToken($token);
        $user = CurrentUser::getUser();
        $user->PushToken = null;
        $user->write();

        return (new JsonApi)->formatReturn([]);
    }

}