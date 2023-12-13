<?php

/**
 * **Authenticate a user**
 *
 * Log a user into the system by generating and returning a new access token
 * Endpoint handles both refresh tokens and logins
 *
 */
class Authorisation_Controller extends Api_Controller
{
    protected $auth = false;

    private static $allowed_actions = array(
        'login'
    );

    private static $url_handlers = array(
        '' => 'login',
    );

    /**
     * Attempt to log the user in
     *
     * Expects data to be sent through as JSON in the $this->requestBody
     *
     * @uses Authorization to handle the authentication of the user
     * @uses JsonApi to return data
     *
     * @return SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function login(SS_HTTPRequest $request)
    {
        if (! $request->isPOST()) $this->handleError(404, 'not found');

        try {
            $response = (new Authorization)->login($this->requestBody);

            return (new JsonApi)->formatReturn($response);

        } catch (\Exception $e) {
            $this->handleError($e->getCode() ?: 1001, $e->getMessage(), 401);
        }
    }

}