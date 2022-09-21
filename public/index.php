<?php
// fixing cors issue
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY,X-APP-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization,Content-length, Referer,Referrer,User-Agent");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
// header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
// this allow preflight headers go through
if($method == "OPTIONS") {
    header("Access-Control-Allow-Headers: X-API-KEY, X-APP-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization,Content-length, Referer,Referrer,User-Agent");
    header("HTTP/1.1 200 OK");
    die();
}
// Path to the front controller (this file)
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

/*
 *---------------------------------------------------------------
 * BOOTSTRAP THE APPLICATION
 *---------------------------------------------------------------
 * This process sets up the path constants, loads and registers
 * our autoloader, along with Composer's, loads our constants
 * and fires up an environment-specific bootstrapping.
 */

// Ensure the current directory is pointing to the front controller's directory
chdir(__DIR__);

// Load our paths config file
// This is the line that might need to be changed, depending on your folder structure.
$pathsConfig = FCPATH . '../app/Config/Paths.php';
// ^^^ Change this if you move your application folder
require realpath($pathsConfig) ?: $pathsConfig;
$paths = new Config\Paths();

// Location of the framework bootstrap file.
$bootstrap = rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';
$app       = require realpath($bootstrap) ?: $bootstrap;

/*
 *---------------------------------------------------------------
 * LAUNCH THE APPLICATION
 *---------------------------------------------------------------
 * Now that everything is setup, it's time to actually fire
 * up the engines and make this app do its thang.
 */
$app->run();
