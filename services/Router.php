<?php

namespace Services;

class Router {


    public static $routes = [];

    public static function load() {
        if(isset(Request::$request->php_admin_resource) && $resource = Request::$request->php_admin_resource) {
            // Resource::load(Request::$request->php_admin_resource, (Array)Request::$request);

            $resource = str_replace('_', ' ', $resource);
            $resource = ucwords($resource);
            $resource = str_replace(' ', '', $resource);
            $resource = '\\App\\Resources\\'.ucwords($resource);

            Resource::load($resource, (Array)Request::$request);
        }
        else {
            Router::redirect(app('entrypoint'));
        }
    }

    public static function redirect(string $name) {
        $url = preg_replace("/^(\/)*/i", "", $name);
        header("location:/$url");
        // echo "<meta http-equiv = 'refresh' content = '0; url = /{$url}' />";
        exit();
    }

    public static function route() {
        return $_SERVER['REQUEST_URI'];
    }

    public static function backLink() {
        if($_SERVER['HTTP_REFERER']) {
            return htmlspecialchars($_SERVER['HTTP_REFERER']);
        }
        return '/';
    }

    public static function back() {
        if($_SERVER['HTTP_REFERER']) {
            header("location:{$_SERVER['HTTP_REFERER']}");
            // echo "<meta http-equiv = 'refresh' content = '0; url = {$_SERVER['HTTP_REFERER']}' />";
            exit();
        }
    }
}

?>
