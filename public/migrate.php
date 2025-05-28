<?php
// public/migrate.php
require __DIR__ . '/../config/config.php';      // boot up your DB connection

// load your SQL file
$schema = file_get_contents(__DIR__ . '/../sql/schema.sql');
if ($schema === false) {
  http_response_code(500);
  exit("❌ Cannot read schema.sql");
}

// execute all statements
if (! $mysqli->multi_query($schema)) {
  http_response_code(500);
  exit("❌ Migration failed: " . $mysqli->error);
}

// flush any additional results
do {
  if ($res = $mysqli->store_result()) { $res->free(); }
} while ($mysqli->more_results() && $mysqli->next_result());

echo "✅ Schema imported successfully.";
