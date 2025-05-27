<?php
// File: config/config.php (env-fallback version)

// 1) Locate .env
$envFile = __DIR__ . '/../.env';
if (!is_file($envFile)) {
    die('.env file not found at ' . $envFile);
}

// 2) Parse .env into an array
$env = parse_ini_file($envFile, false, INI_SCANNER_TYPED);
if ($env === false) {
    die('Failed to parse .env');
}

// 3) Read DB credentials
$dbHost = $env['DB_HOST'] ?? '127.0.0.1';
$dbPort = isset($env['DB_PORT']) ? (int)$env['DB_PORT'] : 3306;
$dbName = $env['DB_NAME'] ?? '';
$dbUser = $env['DB_USER'] ?? '';
$dbPass = $env['DB_PASS'] ?? '';

// 4) Connect to MySQL
$mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);

// 5) Handle connection errors
if ($mysqli->connect_errno) {
    die('DB connect error (' 
      . $mysqli->connect_errno . '): ' 
      . $mysqli->connect_error);
}
