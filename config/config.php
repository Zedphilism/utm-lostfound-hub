<?php
// File: config/config.php

// 1) load Composer autoload
require __DIR__ . '/../vendor/autoload.php';

// 2) tell phpdotenv where to find .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
// safeLoad() so it won't fatal if .env is missing in production
$dotenv->safeLoad();

// 3) grab your variables
$dbHost = $_ENV['DB_HOST']   ?? getenv('DB_HOST');
$dbPort = $_ENV['DB_PORT']   ?? getenv('DB_PORT');
$dbName = $_ENV['DB_NAME']   ?? getenv('DB_NAME');
$dbUser = $_ENV['DB_USER']   ?? getenv('DB_USER');
$dbPass = $_ENV['DB_PASS']   ?? getenv('DB_PASS');

$GOOGLE_API_KEY = $_ENV['GOOGLE_API_KEY'] ?? '';

// === DEBUGGING (uncomment while you’re fixing!) ===
/*
echo '<pre>';
var_dump(compact('dbHost','dbPort','dbName','dbUser','dbPass'));
exit;
*/

// 4) connect
$mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName, (int)$dbPort);
if ($mysqli->connect_error) {
    die('Database Connection Failed: ' . $mysqli->connect_error);
}

// you now have $mysqli for the rest of your app
