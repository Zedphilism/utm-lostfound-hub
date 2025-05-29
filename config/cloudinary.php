<?php
require __DIR__ . '/../vendor/autoload.php';

\Cloudinary\Configuration\Configuration::instance([
  'cloud' => [
    'cloud_name' => 'dvslefxk7',
    'api_key'    => '519647864817983',  // ← corrected key
    'api_secret' => 'B4kGIx56yziJ6o3rVxGWsvFjIIk',
  ],
  'url' => [
    'secure' => true
  ]
]);
