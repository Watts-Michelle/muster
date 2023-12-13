<?php

class Venues_Controller extends Api_Controller {

    protected $auth = true;

    private static $allowed_actions = array(
        'handler',
        'uidHandler',
    );

    private static $url_handlers = array(
        '' => 'handler',
        '$UID' => 'uidHandler',
    );

    /**
     * Venue Endpoints handler
     *
     * @param SS_HTTPRequest $request
     * @return mixed
     */
    public function handler(SS_HTTPRequest $request)
    {
        if (! $request->isGet()) return $this->handleError(404, 'Must be a GET request');

        if($request->isGET()) {
            return $this->getAllVenues($request);
        }
    }

    /**
     * Get All Venues - sorted alphabetically.
     *
     * @param SS_HTTPRequest $request
     * @return SS_HTTPResponse
     */
    public function getAllVenues(SS_HTTPRequest $request)
    {
        $VenuesData = [ 'venues' => []];

        if($Venues = Venue::get()->sort('Name ASC')) {

            foreach($Venues as $venue) {

                if($data = $venue->getData()) {

                    $VenuesData['venues'][] = $data;
                }
            }
        }

        return (new JsonApi)->formatReturn($VenuesData);
    }

    /**
     * Venue Endpoints uidHandler
     *
     * @param SS_HTTPRequest $request
     * @return mixed
     */
    public function uidHandler(SS_HTTPRequest $request)
    {
        if (!($request->isGET())) return $this->handleError(404, 'Must be a GET request.');

        if($request->isGET()) {
            return $this->getSpecificVenue($request);
        }
    }

    /**
     * Get Specific Venue
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function getSpecificVenue(SS_HTTPRequest $request)
    {
        if (! $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $venue = $this->checkUuidExists($request->param('UID'), 'Venue')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        return (new JsonApi)->formatReturn(['venue' => $venue->getData()]);
    }

}