<?php

class VenueOpeningHour extends DataObject
{
    public static $db = array(
        'Day' => 'Enum("Monday, Tuesday, Wednesday, Thursday, Friday, Saturday, Sunday", "Monday")',
        'From' => 'Time',
        'Till' => 'Time'
    );

    private static $has_one = array(
        'Venue' => 'Venue'
    );

    private static $summary_fields = array(
        'Day'
    );

    public function getTitle() {
        return $this->Day;
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        return $fields;
    }

}