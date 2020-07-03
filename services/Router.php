<?php

namespace Services;

class Router {


    public static $routes = [];

    public static function load() {
        if(isset(Request::$request->php_admin_resource) && Request::$request->php_admin_resource) {
            Resource::load(Request::$request->php_admin_resource, (Array)Request::$request);
        }
        else {
            Router::redirect(app('entrypoint'));
            // Resource::(app('entrypoint'));
            // Presenter::present('generics.global_error', ["error_info"=>"Failed", "error_code"=>404, "error_description"=>"PAGE NOT FOUND"]);
        }
    }

    public static function redirect(string $name) {
        $url = preg_replace("/^(\/)*/i", "", $name);
        echo "<meta http-equiv = 'refresh' content = '0; url = /{$url}' />";
    }
}

?>
