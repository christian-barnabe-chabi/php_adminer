<?php

session_start();

use Config\PublicResource;
use Services\Auth;
use Services\Authenticateable;
use Services\Request;
use Services\Resource;
use Services\Route;
use Services\Router;
use Services\Translation;

require_once($_SERVER['DOCUMENT_ROOT'].'/lib/autoload.php');


if(app('must_auth') && !in_array($_SERVER['REQUEST_URI'], PublicResource::routes())) {
    Authenticateable::auth();
} else {
    if(!isset(Request::$request->php_admin_resource)) {
        Resource::load(app('entrypoint'));
    }
}
require_once('routes.php');
Router::load();

?>