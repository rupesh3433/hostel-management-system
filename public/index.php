<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

/*
|--------------------------------------------------------------------------
| Base URL & Base Path (AUTO)
|--------------------------------------------------------------------------
*/
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host     = $_SERVER['HTTP_HOST'];

$scriptDir = dirname($_SERVER['SCRIPT_NAME']); // /project/public
$basePath  = rtrim(str_replace('/public', '', $scriptDir), '');

$baseUrl = $protocol . '://' . $host . $basePath;

define('BASE_URL', $baseUrl);
define('BASE_PATH', $basePath);

$_SESSION['base_url'] = BASE_URL;

/*
|--------------------------------------------------------------------------
| Bootstrap
|--------------------------------------------------------------------------
*/
require_once __DIR__ . '/../app/App.php';
require_once __DIR__ . '/../app/Blade.php';

$app = new App();
$app->run();
