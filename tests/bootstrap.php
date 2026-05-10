<?php

declare(strict_types=1);

$_SERVER['APP_ENV'] = $_SERVER['APP_ENV'] ?? 'test';
$_SERVER['APP_DEBUG'] = $_SERVER['APP_DEBUG'] ?? '1';
$_ENV['APP_ENV'] = $_ENV['APP_ENV'] ?? $_SERVER['APP_ENV'];
$_ENV['APP_DEBUG'] = $_ENV['APP_DEBUG'] ?? $_SERVER['APP_DEBUG'];

$_SERVER['DATABASE_URL'] = $_SERVER['DATABASE_URL'] ?? 'sqlite:///'.str_replace('\\', '/', dirname(__DIR__)).'/var/test.sqlite';
$_ENV['DATABASE_URL'] = $_ENV['DATABASE_URL'] ?? $_SERVER['DATABASE_URL'];
putenv('DATABASE_URL='.$_SERVER['DATABASE_URL']);
putenv('APP_ENV='.$_SERVER['APP_ENV']);
putenv('APP_DEBUG='.$_SERVER['APP_DEBUG']);

require dirname(__DIR__).'/vendor/autoload.php';
