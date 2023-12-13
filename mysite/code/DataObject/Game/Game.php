<?php

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class Game extends DataObject
{
    //Every game has a level limit of 10, to be make editable in the CMS.
    //Every 100 XP means a level up.
    public static $db = array(
        'UUID' => 'Varchar(50)',
        'objectname' => 'Varchar(255)',
        'objectid' => 'Varchar',
        'rating' => 'Varchar',
        'weight' => 'Varchar',
        'own' => 'Varchar',
        'fortrade' => 'Varchar',
        'want' => 'Varchar',
        'wanttobuy' => 'Varchar',
        'wanttoplay' => 'Varchar',
        'preowned' => 'Varchar',
        'preordered' => 'Varchar',
        'wishlist' => 'Varchar',
        'wishlistpriority' => 'Varchar',
        'collid' => 'Varchar',
        'baverage' => 'Varchar',
        'average' => 'Varchar',
        'avgweight' => 'Varchar',
        'rank' => 'Varchar',
        'numowned' => 'Varchar',
        'objecttype' => 'Varchar',
        'originalname' => 'Varchar(255)',
        'minplayers' => 'Varchar',
        'maxplayers' => 'Varchar',
        'playingtime' => 'Varchar',
        'maxplaytime' => 'Varchar',
        'minplaytime' => 'Varchar',
        'yearpublished' => 'Varchar',
        'bgglanguagedependence' => 'Varchar(255)'
    );

    private static $has_one = array(
        'Logo' => 'Image'
    );

    private static $has_many = array(
        'GamingSessions' => 'GamingSession',
        'Levels' => 'Level'
    );

    protected static $indexes = array(
        'UUID' => 'unique("UUID")',
    );

    private static $summary_fields = array(
        'Logo.StripThumbnail' => 'Logo',
        'objectname' => 'Title',
        'minplayers' => 'MinPlayers',
        'maxplayers' => 'MaxPlayers',
        'playingtime' => 'PlayingTime'
    );

    public function onAfterWrite()
    {
        parent::onAfterWrite();

        if (!$this->UUID) {
            $uuid = Uuid::uuid4();
            $this->UUID = $uuid->toString();
            $this->write();
        }
    }

    public function getTitle()
    {
        return $this->objectname;
    }

    public function getGameImage()
    {
        $image = null;

        if ($this->Logo()->ID) {
            $image = $this->Logo()->SetWidth(400)->AbsoluteURL;
        }

        return $image;
    }

    public function getGameThumbnailImage()
    {
        $image = null;

        if ($this->Logo()->ID) {
            $image = $this->Logo()->SetWidth(50)->AbsoluteURL;
        }

        return $image;
    }

    public function getData($user = false)
    {
        $game = [
            'uid' => $this->UUID,
            'title' => $this->objectname,
            'logo' => $this->getGameImage(),
            'thumbnail' => $this->getGameThumbnailImage(),
            'minimum_players' => $this->minplayers,
            'maximum_players' => $this->maxplayers,
            'playing_time' => $this->playingtime,
            'year_published' => $this->yearpublished,
        ];
        
        if($user) {
            
            $xp = $this->calculateXP($user);
            
            $game['experience'] = $xp;
            $game['level'] = self::expToLevel($xp);
            
        }

        return $game;
    }
    
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->fieldByName('Root.Main.UUID')->setattribute('readonly', true);
        $fields->insertBefore('UUID', $fields->fieldByName('Root.Main.Logo'));
        return $fields;
    }
    
    public function calculateXP($user = false) {
        $exp = 0;
        $user ?: $user = CurrentUser::getUser();
        foreach($user->GamingSessionResults() as $result) {
            if($result->GamingSession()->GameID ==  $this->ID) {
                $exp += $result->calculateXP();
            }
        }
            
        return $exp;
    }
    
    public static function expToLevel($exp)
    {
        $level = ceil($exp / 100);
        return $level ? $level : 1;
    }
    
}