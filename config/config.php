<?php

use Services\Presenter;

function app($key, $default = null) {

    if(!isset($_SESSION['ENV'])) {

        $env = "";

        if(!is_file($_SERVER['DOCUMENT_ROOT']."/.env.json")) {
            Presenter::present('generics.top_error', [
                "error_info"=>'Config Failure',
                "error_code"=>111,
                "error_description"=>'env file not exists'
            ]);
            exit();
        }

        $file = fopen($_SERVER['DOCUMENT_ROOT']."/.env.json", "r");

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
            Presenter::present('generics.error', [
                'error_code' => 1472,
                'error_info' => 'Configuration',
                'error_description' => "Configuration error. It seems like the <code>.env.json</code> file is not well formated",
            ]);
            exit('env error');
        }

        if(!isset($env['primary_color']) || !in_array(trim($env['primary_color']), $colors )) {
            $env['primary_color'] = 'red';
        }

        if(!isset($env['lang'])) {
            $env['lang'] = 'en';
        }

        if(!isset($env['date_format'])) {
            $env['date_format'] = 'Y-m-d';
        }

        $_SESSION['ENV'] = $env;
    }
    
    return $_SESSION['ENV'][$key] ?? $default;

}

function config($key, $value) {
    $_SESSION['ENV'][$key] = $value;
}

function url() {
    $req_url = explode('?', strtolower($_SERVER['REQUEST_URI']))[0];
    return preg_replace("/(\/+)$/", "", $req_url).'/';
}

?>