<?php
/*
 * This file is part of the kleijnweb/swagger-bundle-example package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
require_once __DIR__ . "/../app/autoload.php";
require_once __DIR__ . "/../app/AppKernel.php";

use Symfony\Component\HttpFoundation\Request;
use Dotenv\Dotenv;

$dotenv = new Dotenv(__DIR__ . '/../');
$dotenv->load();

if ($_SERVER['SYMFONY_ENV'] == 'dev') {
    umask(0000);
}

// Some old school PHP does the trick fast and predictable
$requestUri = $_SERVER['REQUEST_URI'];
if (0 === strpos($requestUri, '/swagger/') && preg_match('/^\/swagger\/[\/a-z0-9\-]+\.yml$/', $requestUri)) {
    $path = __DIR__ . "/$requestUri";
    header('Content-Type: text/yml;charset=UTF-8');
    header('Content-Length: ' . filesize($path));
    readfile($path);
    exit;
}

if (0 === strpos($_SERVER['SERVER_SOFTWARE'], 'PHP ')) {
    // Built-in server: assuming dev
    $dotenv->overload();
}

$request = Request::createFromGlobals();
$kernel = new AppKernel($_SERVER['SYMFONY_ENV'], (bool)$_SERVER['SYMFONY_DEBUG']);
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
