<?php

class DailySessionPush extends BuildTask {
 
    protected $title = 'Daily Session Push';
 
    protected $description = 'Push notifications to attendees if session is happening today. Should run one time per day.';
 
    protected $enabled = true;
 
    function run($request) {
        $push = new Fcm;
        $results = array();
        $todaysGamingSessions = GamingSession::get()->filter(['GameStatus' => 'Active', 'StartDate:PartialMatch' => date('Y-m-d')]);
                
        foreach($todaysGamingSessions as $todaysGamingSession) {
            
            try {
                
                $tokens = [];
                $attendees = $todaysGamingSession->getAttendees();
                
                foreach($attendees as $attendee) {
                    
                    if(!empty($attendee->PushToken)) {
                        
                       $tokens[] = $attendee->PushToken;
                    }
                }
                
                $msg = 'Your gaming session "' . $todaysGamingSession->Name . '" starts today';
                
                if($todaysGamingSession->Venue()->ID) {
                    
                    $msg.= ' at '.$todaysGamingSession->Venue()->Location;
                
                } else {
                    
                    $msg.= ' at '.$todaysGamingSession->Location()->Location;
                    
                }

                $results[$todaysGamingSession -> UUID] = $push->send($tokens, 'gamingsessiondaily', [
                    'gamingsession_uid' => $todaysGamingSession->UUID , 
                    'gamingsession_start_date' => (int)$todaysGamingSession->dbObject('StartDate')->format('U'), 
                    'gamingsession_end_date' => (int)$todaysGamingSession->dbObject('EndDate')->format('U'), 
                    'gamingsession_place_name' => $todaysGamingSession->Venue()->exists() ? $todaysGamingSession->Venue()->Location : $todaysGamingSession->Location()->Location,
                    'gamingsession_name' => $todaysGamingSession->Name, 
                    'gamingsession_host' => $todaysGamingSession->Member() ? $todaysGamingSession->Member()->FirstName . ' ' . $todaysGamingSession->Member()->Surname : '',
                ], $msg);
                
            } catch(O_HTTPResponse_Exception $e) {
                
                echo $e->getMessage();
                
            } catch(Exception $e) {
                
                echo $e->getMessage();
                
            }
        }
        
        return $results;
    }
}