<?php

/* Change this from 'dev' to 'live' for a production environment. */
define('SS_ENVIRONMENT_TYPE', 'dev');

/* This defines a default database user */
define('SS_DATABASE_SERVER', 'localhost');
define('SS_DATABASE_USERNAME', 'Muster');
define('SS_DATABASE_PASSWORD', 'Muster');
define('SS_DATABASE_NAME', 'Muster');
global $_FILE_TO_URL_MAPPING;

$_FILE_TO_URL_MAPPING[''] = '';

define('SS_DEFAULT_ADMIN_USERNAME', 'admin');
define('SS_DEFAULT_ADMIN_PASSWORD', 'password');

define('API_CLIENT_PUBLIC', 'muster_dev');
define('API_CLIENT_SECRET', 'nMPIWmlocyFeNdmpogcKNEGDYnqodeKIcmGLsuqbkUocmW');

define('FACEBOOKAPPID', '1305204739560686');