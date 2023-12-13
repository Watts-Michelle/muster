<?php

class Badges_Controller extends Api_Controller {

    protected $auth = true;

    private static $allowed_actions = array(
        'handler',
        'uidHandler',
    );

    private static $url_handlers = array(
        '' => 'handler',                   // GET
        '$UID' => 'uidHandler',            // GET
    );

    /**
     * Badge Endpoints handler
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function handler(SS_HTTPRequest $request)
    {
        if (! $request->isGet()) return $this->handleError(404, 'Must be a GET request');

        if($request->isGET()) {
            return $this->getAllBadges($request);
        }
    }

    /**
     * Get All Badges
     *
     * @param SS_HTTPRequest $request
     * @return SS_HTTPResponse
     */
    public function getAllBadges(SS_HTTPRequest $request)
    {
        $BadgesData = [
            'badges' => []
        ];

        foreach(Badge::get()->sort('Created ASC') as $badge) {
            if($data = $badge->getData()) {
                $BadgesData['badges'][] = $data;
            }
        }

        return (new JsonApi)->formatReturn($BadgesData);
    }

    /**
     * Badge Endpoints uidHandler
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function uidHandler(SS_HTTPRequest $request)
    {
        if (!($request->isGET())) return $this->handleError(404, 'Must be a GET request');

        if($request->isGET()) {
            return $this->getSpecificBadge($request);
        }
    }

    /**
     * Get Specific Badge
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function getSpecificBadge(SS_HTTPRequest $request)
    {
        if (! $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $Badge = $this->checkUuidExists($request->param('UID'), 'Badge')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        return (new JsonApi)->formatReturn(['badge' => $Badge->getData()]);
    }

}