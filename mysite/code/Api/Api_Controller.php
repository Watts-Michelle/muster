<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Respect\Validation\Validator as v;

/**
 * Base controller for API
 *
 * Contains common functions used across multiple methods in the API
 * Also handles authentications for other Controllers
 */
class Api_Controller extends Controller {

    /** @var Member The logged in user */
    protected $appUser;

    /** @var  array Decoded JSON body from request */
    protected $requestBody;

    /** @var  AuthResource The login server */
    protected $authServer;

    /** @var bool Does this controller use auth? */
    protected $auth = true;

    protected $settings;

    protected $unverified = array();

    /** @var array defining allowed actions for this controller */
    private static $allowed_actions = array();

    /** @var array defining URL rules for this controller */
    private static $url_handlers = array();

    /** @var  \Monolog instance */
    protected $logger;

    /**
     * Assign the request body to variable
     * Authenticate the user if the controller requires it
     */
    public function init() {
        parent::init();

        $this->settings = SiteConfig::current_site_config();

        if ($this->auth) {
            //check authentication
            try {
                $this->authServer = new AuthResource;
                $this->appUser = $this->authServer->getLoggedInUser();
                $this->appUser->markAsActive();
                CurrentUser::setUser($this->appUser);

            } catch (League\OAuth2\Server\Exception\InvalidRequestException $e) {
                $this->handleError(401, 'Authentication header missing or invalid');
            } catch (League\OAuth2\Server\Exception\AccessDeniedException $e) {
                $this->handleError(1002, 'Session invalid', 401);
            }

            $this->appUser->LastAccess = date('Y-m-d H:i:s');
            $this->appUser->write();
        }

        $this->requestBody = $this->getBody($this->request);

        if (!empty($this->settings->LogDirectory)) {
            $this->logger = new Logger('name');
            $this->logger->pushHandler(new StreamHandler($this->settings->LogDirectory, Logger::DEBUG));
        }
    }

    /**
     * @param int $errorCode Error number
     * @param string $message Error message
     * @param int $httpStatus Error status
     * @throws O_HTTPResponse_Exception
     * @return bool
     */
    public function handleError($errorCode, $message = null, $httpStatus = 400)
    {
        if ($errorCode == 404) $httpStatus = 404;
        if ($errorCode == 401) $httpStatus = 401;

        $errorCodeObj = new ErrorCodes;

        if (! $message) {
            $message = $errorCodeObj->config()->{$errorCode};
        }

        throw new O_HTTPResponse_Exception($message, $httpStatus, $errorCode);
        return false;
    }

    /**
     * Get request body from POST data
     *
     * @param SS_HTTPRequest $request
     * @return mixed
     */
    public function getBody(SS_HTTPRequest $request)
    {
        $body = $request->getBody();
        
        return json_decode($body, true);
    }

    public function performUpload($UploadFile, DataObject $Object, $FolderName)
    {
        if ( ! $type = $this->imageValidator($UploadFile)) {
            return $this->handleError(2001, 'Image file not valid.');
        }

        $upload = new Upload;
        $upload->load($UploadFile, $FolderName .'/' . $Object->UUID . '/' . $type);

        $file = $upload->getFile();

        return $file;
    }

    /**
     * Check if image is:
     * png, jpg, jpeg
     * and smaller than 10mb.
     */
    public function imageValidator($uploadfile)
    {
        $validator = new Upload_Validator();
        $validator->setTmpFile($uploadfile);
        $validator->setAllowedMaxFileSize(10000000);
        $validator->setAllowedExtensions(array('png', 'jpg', 'jpeg'));

        if ($valid = $validator->validate()) {
            return 'image';
        }

        return false;
    }

    public function checkUuidExists($uid, $objectType)
    {
        if($obj = DataObject::get($objectType)->filter('UUID', mb_strtolower($uid))->first()){
            return $obj;
        } else {
            return false;
        }
    }

    public function checkMultipartContentType($string)
    {
        if (preg_match('/^multipart\/form-data/i', $string)) {
            return true;
        }
    }

    public function isCurrentUser($UID)
    {
        if(CurrentUser::getUserUUID() != $UID){
            return false;
        }

        return true;
    }
    
    protected function _changeObjectStatusById($objectID, $type, $status, $restrictedByOwner = false) {
        if(! $object = $this->checkUuidExists($objectID, $type)) {
            return false;
        }
        
        if($restrictedByOwner && property_exists($object, $restrictedByOwner)) {
            if($object->{$restrictedByOwner} != CurrentUser::getUserID()){
                return $this->handleError(404, 'TO DO: Whoops, you can not edit this object!');
            }
        }
        
        $objectStatus = DB::get_schema()->enumValuesForField(new $type, 'Status');
        
        if(!v::stringType()->in($objectStatus)->validate($status)){
            return $this->handleError(404, 'TO DO: Status not valid for this object!');
        }
        
        try {
            $object->Status = $status;
            $object->write();
            return true;
            
        } catch (Exception $e) {
            return $this->handleError(5000, $e->getMessage(), 400);
        }
    }
}