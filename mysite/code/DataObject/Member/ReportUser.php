<?php

class ReportUser extends DataObject
{
    public static $db = array(
        'Reported' => 'Boolean',
        'Message' => 'Text'
    );

    private static $has_one = array(
        'Reporter' => 'Member',
        'Reportee' => 'Member'
    );

    private static $summary_fields = array(
        'Reporter.Name' => 'Reporter',
        'Reportee.Name' => 'Reportee',
        'Message' => 'Report Message',
        'Created' => 'Created',
//        'Reportee' => 'ReporteeUser'
    );
}