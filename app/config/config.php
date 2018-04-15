<?php

#Defining database constants

define('DB_HOST', 'localhost');
define('DB_NAME','somalibooks');
define('DB_USER','root');
define('DB_PASS','');
$key = bin2hex(random_bytes(8));
define('SECRET','MAMA');


#Defining site wide constants
define('APPROOT',dirname(dirname(__FILE__)));

define('URLROOT','http://localhost/somalibooks');

define('SITENAME','SomaliBooks');


