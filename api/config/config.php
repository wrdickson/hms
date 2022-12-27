<?php


namespace wrdickson\apibook;

//  dev on localhost
define('DB_HOST', 'localhost');
define('DB_USER', 'mto_admin');
define('DB_PASS', 'quetzal123');
define('DB_NAME', 't_book_db');
//  servername for jwt authentication
define('SERVER_NAME', 'localhost');

//  production
/*
define('DB_HOST', 'localhost');
define('DB_USER', 'trekbill_some_db_user');
define('DB_PASS', 'Seafoam_123');
define('DB_NAME', 'trekbill_ezrent');
//  servername for jwt authentication
define('SERVER_NAME', 'my/production/server/location');
*/

//  secret for firebase/php-jwt
define('JWT_KEY', 'wx9BviAiHrv52l3UnxqfrSdZFgBlWeN4d8qJFfb7biDYsK2hshuAB8kuT62UB0');