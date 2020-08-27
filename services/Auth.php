<?php

namespace Services;

use Services\API;
use Services\Request;
use Services\URL;

use function Lib\deep_walk;

class Auth {

    public static function attempt($email, $password)
    {
        $api = new API();

        $data = [
            "email"=>$email,
            "password"=>$password
        ];
        

        $url = app('baseUrl').app('loginEndpoint');

        $login_method = app('loginMethod');

        $login_method = $login_method ? $login_method : 'POST';

        $response = $api->callWith($url, $login_method, $data)->response();

        if(deep_walk($response, app('tokenKey', 'token'))) {
            $_SESSION['oauth'] = $response;
        }

        self::user();
        if(self::user()) {
            self::authenticated();
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

    public static function authenticated() {
        $url = $_SERVER['REQUEST_URI'];
        Router::redirect($url);
    }

}
?>
