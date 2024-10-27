<?php


require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$uri = explode('?',$_SERVER['REQUEST_URI']);
$currentLang = 'pt_BR';
if (!empty($_COOKIE['LANG'])){
    $currentLang = $_COOKIE['LANG'];
    $_ENV['LANG'] = $_COOKIE['LANG'];
}

if (isset($uri) && !empty($uri[1])){    
    $lang = explode('=', $uri[1]);
    if (empty($_COOKIE['LANG'])){
        setcookie('LANG', $lang[1], time() + (86400 * 30), "/");  
        $currentLang = $lang[1];
    }
    else{
        if ($_COOKIE['LANG'] != $lang[1]){
            setcookie('LANG', $lang[1], time() + (86400 * 30), "/");
            $currentLang = $lang[1];    
        }        
    }   
    $_ENV['LANG'] = $currentLang;    
}


require_once __DIR__ . "/lib/framework/Core/Constants.php";
require_once __DIR__ . "/lib/framework/Core/ResourceManager.php";
require_once __DIR__ . "/lib/framework/Resources/Dictionary.php";


spl_autoload_extensions('.php');
spl_autoload_register();

use Areas\Access\Access;
use Areas\User\User;
use Lib\Framework\Core\SecurityService;
use Steampixel\Route;


function cookieToSession($cookieName)
{
    if (isset($_COOKIE[$cookieName])) {
        // Get the serialized session data from the cookie. Check ClientViewController::MercadoPagoCreatePreference. We generate the cookie there
        $sessionDataSerialized = $_COOKIE[$cookieName];
        // Unserialize and set it back into $_SESSION
        $_SESSION = unserialize($sessionDataSerialized);
        //remove cookie
        setcookie($cookieName, "", time() - 3600, '/');
    }
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
    cookieToSession(APPDEFAULTCOOKIE);
    $sessionExpiration = 15 * 60; // 15 minutes in seconds
    // Check if the session is active
    if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > $sessionExpiration) {
        // Destroy the session
        session_unset();
        session_destroy();
        // Redirect to the login page
        header('Location: ' . BASE_URI . 'login/signin');
        exit;
    }
    // Update the last activity time
    $_SESSION['last_activity'] = time();
}

function getVisitorIpAddress() {
    // Check for shared internet/ISP IP
    if (!empty($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }

    // Check for IP address from a proxy
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipAddresses = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        foreach ($ipAddresses as $ip) {
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }

    // Check for remote IP address
    if (!empty($_SERVER['REMOTE_ADDR']) && filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP)) {
        return $_SERVER['REMOTE_ADDR'];
    }

    // Return a fallback IP address
    return 'Unknown';
}

// Functions for fingerprinting
function generateFingerprint($data)
{
    return hash_hmac('sha512', $data, HEADER_SECRET_KEY);
}

function validateFingerprint($fingerprint, $data)
{
    $expectedFingerprint = generateFingerprint($data);
    return hash_equals($expectedFingerprint, $fingerprint);
}

function isAuthorized($controllerName, $method)
{
    $oUser = new User(CONNECTION_STRING);

    if (isset($_SESSION['user'])) {
        return $oUser->userIsAuthorized($controllerName, $method);
    }

    return false;
}

function isThisRouteFree($controllerName, $method) 
{
    $oAccess = new Access(CONNECTION_STRING);
    $permissions = $oAccess->getExternalAccess();

    if ($permissions) {
        foreach ($permissions as $permission) {
            if (strtolower($controllerName) == strtolower($permission['classe']) && strtolower($method) == strtolower($permission['metodo'])) {
                return true;
            }
        }
    }

    return false;
}

function validateRequest($methodName=null)
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $securityService = new SecurityService();

        if (isset($_POST['token_key'])) {
            return true;
        }

        $validateToken = $securityService->validateToken($_POST['csrf_token'], $methodName);

        if (!$validateToken) {
            header("HTTP/1.1 401 Not Authorized");
            die(0);
        }
    }
}

function appAccessLog()
{
    // Log the access to the app
    $logFile = __DIR__ . '/.logs/access.log';
    $record = date('Y-m-d H:i:s') . ' | ' 
    . getVisitorIpAddress()  . ' | ' 
    . $_SERVER['HTTP_HOST'] . ' | ' 
    . $_SERVER['REQUEST_URI'] . ' | ' 
    . $_SERVER['REQUEST_METHOD'] .' | '
    . $_SERVER['HTTP_USER_AGENT'] .  "\n";

    error_log($record, 3, $logFile);
}

// Check fingerprint for subsequent requests
$timestamp = $_COOKIE['X-Timestamp'] ?? '';
$fingerprint = $_COOKIE['X-Fingerprint'] ?? '';

if ($timestamp && $fingerprint) {
    if (!validateFingerprint($fingerprint, $timestamp)) {
        header("HTTP/1.0 403 Forbidden");
        exit;
    }
} else {
    // Initial request handling - set cookies
    $responseTimestamp = time();
    $responseFingerprint = generateFingerprint($responseTimestamp);
    setcookie("X-Timestamp", $responseTimestamp, [
        'expires' => 0,
        'path' => '/',
        'domain' => '', // set to your domain if needed
        'secure' => '',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    setcookie("X-Fingerprint", $responseFingerprint, [
        'expires' => 0,
        'path' => '/',
        'domain' => '', // set to your domain if needed
        'secure' => '',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}

$app = function ($controllerName, $methodName, $params) {
    $params = explode('/', $params);
    $fullClassName = APP_PATH . '\\' . ucfirst($controllerName) . '\\' . ucfirst($controllerName) . 'Controller';
    $params = $params[0] == '' ? 0 : $params;

    if (class_exists($fullClassName, true)) {
        validateRequest($methodName);
        $className = new $fullClassName();
        $controller = new \ReflectionClass($className);
        $object = $controller->newInstance();
        $routes = $object->getAllRoutes();
        if (is_array($routes)) {
            $methodName = $methodName == '' ? 'index' : $methodName;
            if (key_exists($methodName, $routes)) {
                $realMethodName = $routes[$methodName]['method'];
                if (isThisRouteFree($controllerName, $realMethodName)) { // Checks if user is trying to access a external route.
                    appAccessLog();
                    call_user_func_array(array($object, $realMethodName), (array)$params);
                } else if (isAuthorized($controllerName, $realMethodName)) {
                    appAccessLog();
                    call_user_func_array(array($object, $realMethodName), (array)$params);
                }
                else { //The current user is not authorized to perform the requested action                    
                    header("HTTP/1.0 403 Forbidden");
                }
            } else { //Method is not a route
                header("HTTP/1.0 400 Bad Request");
            }
        } else { //The controller doesn't have any routes
            header("HTTP/1.0 400 Bad Request");
        }
    } else { //The class doesn't exists
        header("HTTP/1.0 404 Not Found");
    }
};

Route::add(BASE_URI . '([^/]+)/?([^/]+)?/?(.*)?', $app, array('GET', 'POST', 'DELETE', 'PUT'));
// Run the router
Route::run('/', false, true);
