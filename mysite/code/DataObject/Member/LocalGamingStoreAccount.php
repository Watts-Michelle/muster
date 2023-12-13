<?php

class LocalGamingStoreAccount extends DataObject
{
    public static $db = array(
        'Status' => 'Enum("False, True, Pending")',
    );

    private static $has_one = array(
        'Member' => 'Member',
    );
}