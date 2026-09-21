<?php
declare(strict_types=1);
if (!defined('ADMIN_PANEL_ACTIVE')) { http_response_code(403); exit('Direct access not permitted.'); }
return array (
  'users' => 
  array (
    'admin' => 
    array (
      'username' => 'admin',
      'password_hash' => '$2y$10$ya82MTXhwtcg4eg7.NPxIOqRM9qesAO3HLPQYuIWN6yeZGXO6QD/e',
      'name' => 'Prospect Administrator',
      'email' => 'hello@prospectdigital.in',
      'role' => 'superadmin',
      'created_at' => '2026-09-18T16:40:00+05:30',
      'last_login' => '2026-09-21T11:47:22+05:30',
    ),
  ),
  'settings' => 
  array (
    'session_timeout' => 7200,
    'max_login_attempts' => 5,
    'lockout_time' => 900,
  ),
);
