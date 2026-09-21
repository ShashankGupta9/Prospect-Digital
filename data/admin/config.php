<?php
declare(strict_types=1);
if (!defined('ADMIN_PANEL_ACTIVE')) { http_response_code(403); exit('Direct access not permitted.'); }
return array (
  'users' => 
  array (
    'admin' => 
    array (
      'username' => 'admin',
      'password_hash' => '$2y$10$N3OukP7DdNevkpL6ojzlaeWA3Wl3MgwVX3LzQYUcExISLt.mVYDmO',
      'name' => 'Prospect Administrator',
      'email' => 'hello@prospectdigital.in',
      'role' => 'superadmin',
      'created_at' => '2026-09-18T16:40:00+05:30',
      'last_login' => '2026-09-19T16:25:23+05:30',
    ),
  ),
  'settings' => 
  array (
    'session_timeout' => 7200,
    'max_login_attempts' => 5,
    'lockout_time' => 900,
  ),
);
