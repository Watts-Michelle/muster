<?php

use League\OAuth2\Server\Exception\OAuthException;

/**
 * Exception class
 */
class UserDeletedException extends OAuthException
{

    public $httpStatusCode = 401;


    public $errorType = 'user_deleted';


    public function __construct()
    {
        parent::__construct('Your account is deleted.');
    }
}