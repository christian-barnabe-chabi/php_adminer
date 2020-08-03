<?php

namespace Services;

use Services\API;
use Services\Request;
use Services\URL;

use function Lib\deep_walk;

class Auth {

    public function __construct($email = "", $password = "")
    {

        $api = new API();

        $data = [
            "email"=>Request::$request->email,
            "password"=>Request::$request->password
        ];
        

        $url = app('base_url').app('login_endpoint');

        $login_method = app('login_method');

        $login_method = $login_method ? $login_method : 'POST';

        $response = $api->callWith($url, $login_method, $data)->response();

        if(deep_walk($response, 'token')) {
            $_SESSION['oauth'] = $response;
        }

        if($this->user()) {
            $this->authenticated();
        }
        
    }

    public static function token() {
        if(isset($_SESSION['oauth'])) {
            return deep_walk((Array)$_SESSION['oauth'], 'token');
        } else {
            return null;
        }
    }

    public static function user() {
        if(isset($_SESSION['oauth'])) {
            return (Object)$_SESSION['oauth'];
        } else {
            return null;
        }
    }

    public function authenticated() {
        $url = $_SERVER['REQUEST_URI'];
        Router::redirect($url);
    }

}
?>
