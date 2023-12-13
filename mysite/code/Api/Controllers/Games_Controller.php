<?php

class Games_Controller extends Api_Controller {

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
     * Game Endpoints handler
     *
     * @param SS_HTTPRequest $request
     * @return mixed
     */
    public function handler(SS_HTTPRequest $request)
    {
        if (! $request->isGet()) return $this->handleError(404, 'Must be a GET request');

        if($request->isGET()) {
            return $this->getAllGames($request);
        }
    }

    /**
     * Get All Games - sorted alphabetically.
     *
     * @param SS_HTTPRequest $request
     * @return SS_HTTPResponse
     */
    public function getAllGames(SS_HTTPRequest $request)
    {
        $GamesData = [ 'games' => []];

        if($Games = Game::get()->sort('objectname ASC')) {

            foreach($Games as $game) {

                if($data = $game->getData()) {

                    $GamesData['games'][] = $data;
                }
            }
        }

        return (new JsonApi)->formatReturn($GamesData);
    }

    /**
     * Game Endpoints uidHandler
     *
     * @param SS_HTTPRequest $request
     * @return mixed
     */
    public function uidHandler(SS_HTTPRequest $request)
    {
        if (!($request->isGET())) return $this->handleError(404, 'Must be a GET request.');

        if($request->isGET()) {
            return $this->getSpecificGame($request);
        }
    }

    /**
     * Get Specific Game
     *
     * @param SS_HTTPRequest $request
     * @return bool|SS_HTTPResponse
     * @throws O_HTTPResponse_Exception
     */
    public function getSpecificGame(SS_HTTPRequest $request)
    {
        // PERMISSION - TO DO: Set restrictions on seeing session invitations and join requests??

        if (! $request->param('UID')) {
            return $this->handleError(404, 'TO DO: Malformed URL - or some other error message!');
        }

        if(! $game = $this->checkUuidExists($request->param('UID'), 'Game')) {
            return $this->handleError(404, 'TO DO: Wrong UID!!');
        }

        return (new JsonApi)->formatReturn(['game' => $game->getData()]);
    }

}