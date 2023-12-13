<?php

class GamingSessionResult extends DataObject
{
    public static $db = array(
        'Result' => 'Enum(", Victory, Defeat, Stalemate, No Session", "")',
    );

    private static $has_one = array(
        'GamingSession' => 'GamingSession',
        'ExperiencePoint' => 'ExperiencePoint',
        'Commend' => 'Commend',
        'Member' => 'Member'
    );

    /**
     * Gaming Session Result response
     *
     * @return array
     */
    public function getData()
    {
        $data = [
            'uid' => $this->Member()->UUID,
            'username' => $this->Member()->Username,
            'facebook_name' => $this->Member()->FacebookName,
            'xp' => $this->ExperiencePoint()->Points,
            'result' => $this->Result,
        ];

        return $data;
    }
    
    public function onAfterWrite()
    {
        parent::onAfterWrite();
    
        apc_delete('member-'.$this -> MemberID );
        
    }
    
    public function onAfterDelete() {
        
        apc_delete('member-'.$this -> MemberID );
        
        parent::onAfterDelete();
    }
    
    public function calculateXP() {
        $exp = (int)$this->ExperiencePoint()->Points;
                    
        if($this->Commend()->exists()) {
            $exp += (int)$this->Commend()->ExperiencePoint()->Points;
        }
        
        return $exp;
    }
}