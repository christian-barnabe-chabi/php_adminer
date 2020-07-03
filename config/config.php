<?php

use Services\Presenter;

function app($key) {

    if(!isset($_SESSION['ENV'])) {

        $env = "";

        if(!is_file($_SERVER['DOCUMENT_ROOT']."/.env")) {
            Presenter::present('generics.top_error', [
                "error_info"=>'Config Failure',
                "error_code"=>111,
                "error_description"=>'env file not exists'
            ]);
            exit();
        }

        $file = fopen($_SERVER['DOCUMENT_ROOT']."/.env", "r");

        while($line = fgets($file)) {
            $env .= $line;
        }

        $env = json_decode($env, true);

        $colors = [
            'red', 
            'orange', 
            'yellow', 
            'olive', 
            'green', 
            'teal', 
            'blue', 
            'violet', 
            'purple', 
            'pink', 
            'brown', 
            'grey', 
            'black'
        ];

        if($env == null) {
            Presenter::present('generics.top_error', []);
            exit('env error');
        }

        if(!isset($env['primary_color']) || !in_array(trim($env['primary_color']), $colors )) {
            $env['primary_color'] = 'blue';
        }

        if(!isset($env['lang'])) {
            $env['lang'] = 'en';
        }

        if(!isset($env['date_format'])) {
            $env['date_format'] = 'Y-m-d';
        }

        $_SESSION['ENV'] = $env;
    }
    
    return isset($_SESSION['ENV'][$key] ) ? $_SESSION['ENV'][$key] : null;

}

function config($key, $value) {
    $_SESSION['ENV'][$key] = $value;
}

function url() {
    $req_url = explode('?', strtolower($_SERVER['REQUEST_URI']))[0];
    return preg_replace("/(\/+)$/", "", $req_url).'/';
}

?>