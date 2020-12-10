<?php
// header('Cache-Control: no cache'); //no cache
// session_cache_limiter('private_no_expire'); // works
header("Access-Control-Allow-Origin: *");
session_start();
// setcookie('samesite-test', '1', 0, '/; samesite=strict');

use App\Managers\Dashboard;
use Config\PublicResource;
use Services\Auth;
use Services\Authenticateable;
use Services\Request;
use Services\Resource;
use Services\Router;

require_once($_SERVER['DOCUMENT_ROOT'].'/lib/autoload.php');

if(app('mustAuth')) {
    if(isset(Auth::user()->role->type) && Auth::user()->role->type == 'ADMINISTRATOR') {
        config('entrypoint', 'dashboard');
    } 
}

if(app('mustAuth') && !in_array($_SERVER['REQUEST_URI'], PublicResource::routes())) {
    Authenticateable::auth();
} else {
    if(!isset(Request::$request->php_admin_resource)) {
        Resource::load(app('entrypoint', 'user'));
    }
}

require_once($_SERVER['DOCUMENT_ROOT'].'/routes.php');

Router::load();

?>