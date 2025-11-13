<?php
return [
  'db' => [
    'dsn' => 'mysql:host=127.0.0.1;dbname=apiphp;charset=utf8mb4',
    'user' => 'root',
    'pass' => ''
  ],
  'app' => ['local',
  'debug' => true,
  'base_url' => 'http://localhost/api-php-native-irenet/public',
  'jwt_secret' => 'Cinta_sejati_takkan_pernah_pudar_oleh_waktu>=32_chars',
  'allowed_origins' => ['http://localhost:3000','http:localhost']
  ]
];