<?php

global $project;
$project = 'mysite';

// Use _ss_environment.php file for configuration
require_once("conf/ConfigureFromEnv.php");
require_once '../vendor/autoload.php';

// Set the site locale
i18n::set_locale('en_US');
CMSMenu::remove_menu_item('ReportAdmin');
HTTP::set_cache_age(86400);