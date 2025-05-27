<?php
// testenv.php debug

header('Content-Type: text/plain');

// 1) Where we are
echo "Directory: " . __DIR__ . "\n\n";

// 2) Path to .env
$envPath = __DIR__ . '/.env';
echo "Looking for .env at: {$envPath}\n";
echo "Exists? " . (is_file($envPath) ? 'YES' : 'NO') . "\n\n";

// 3) If it exists, dump its contents
if (is_file($envPath)) {
    echo "== .env contents start ==\n";
    echo file_get_contents($envPath) . "\n";
    echo "== .env contents end ==\n\n";
}

// 4) Attempt to load via Dotenv
require __DIR__ . '/vendor/autoload.php';
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// 5) Show what getenv() returns
echo "ENV VARS:\n";
echo "DB_HOST=" . getenv('DB_HOST') . "\n";
echo "DB_PORT=" . getenv('DB_PORT') . "\n";
echo "DB_NAME=" . getenv('DB_NAME') . "\n";
echo "DB_USER=" . getenv('DB_USER') . "\n";
echo "DB_PASS=" . (getenv('DB_PASS') ? '[set]' : '[empty]') . "\n";
