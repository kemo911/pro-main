<?php
ini_set('display_errors', 1);
////////////////////
// Important ! These must be filled in correctly.
// Database details are required to use this script.
global $dbConfig;
$host = "localhost"; // If you don't know what your host is, it's safe to leave it localhost
$dbName = "bosseesg_promutuel"; // Database name
$dbUser = "bosseesg_user"; // Username
$dbPass = "bosseesg@password123"; // Password

$dbConfig['host'] = $host;
$dbConfig['name'] = $dbName;
$dbConfig['user'] = $dbUser;
$dbConfig['pass'] = $dbPass;
