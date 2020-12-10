<?php

namespace Services;

use Services\Presenter;
use Throwable;

use function Lib\plurial_noun;

class Resource
{
    private static $resource;
    private static $page_title;
    private static $generated_code;
    private static $handle_methode_name = "handle";
    private static $endpoints = null;

    public static function load(string $resource_class, array $data = [], $handle_methode_name = 'handle')
    {
        // if(is_link($_SERVER['DOCUMENT_ROOT'].'/server/public') == false){
        //     symlink($_SERVER['DOCUMENT_ROOT'].'/public', $_SERVER['DOCUMENT_ROOT'].'/server/public');
        // }


        $data = array_merge((Array)Request::$request, $data);

        self::$resource = new $resource_class($data);

        if (method_exists(self::$resource, 'get_model_name')) {
            self::$page_title = self::$resource->get_model_name() .' | ';
        } else {
            self::$page_title = Request::$request->php_admin_resource ? Translation::translate(Request::$request->php_admin_resource).' | ' : ' ';
        }

        if(method_exists(self::$resource, 'get_endpoints')) {
            self::$endpoints = self::$resource->get_endpoints();
        }

        self::$page_title = str_replace('_', ' ', self::$page_title);
        self::$page_title = ucfirst(self::$page_title);
        self::open_tags();

        $handler = self::$handle_methode_name;
        if ($handler && method_exists(self::$resource, $handler)) {
            self::$resource->$handler($data);
            self::close_tags();
        } else {
            Presenter::present("generics.error", [
                "error_info" => Translation::translate('failure'),
                "error_code" => 87,
                "error_description"=>Translation::translate('the_class')."'". get_class(self::$resource) ."' ". Translation::translate('must_implement_method') ." '".self::$handle_methode_name."'"
            ]); 
            self::close_tags();
        }
        self::close_tags();
        exit();
    }

    public static function open_tags()
    {

        $title = ucfirst(strtolower(self::$page_title)) . strtoupper(app('appName'));
        $favicon = app('icon');
        $robot = app('robots', 'noindex');
        $secondaryColor = app('secondaryColor', 'rgba(0, 172, 29, 0.979)');
        echo "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <meta name='robots' content='$robot'>
            <link rel='shortcut icon' href='{$favicon}'>
            <title>{$title}</title>
            <link rel='stylesheet' href='/public/SemanticUI/semantic.min.css'>
            <link rel='stylesheet' href='/public/uikit/css/uikit.min.css'>
            <link rel='stylesheet' href='/public/uikit/css/uikit-rtl.min.css'>
            <link rel='stylesheet' href='/public/css/main.css'>

            <script src='/public/js/jquery.js'></script>
            <script src='/public/SemanticUI/semantic.min.js'></script>
            <script src='/public/js/modes.js'></script>
            <script src='/public/SemanticUI/tablesort.js'></script>
            <script defer src='/public/uikit/js/uikit-icons.min.js'></script>
            <script defer src='/public/uikit/js/uikit.min.js'></script>
            <script src='/public/js/chart.js'></script>
            <script src='/public/js/php_admin_script.js' defer></script>
            <script src='/public/js/main.js'></script>
            <script src='/public/js/example.js' defer></script>
            <script src='/public/js/resourceFunctions.js'></script>
            <script src='/public/js/paginator.js' defer></script>
            <script src='/public/js/table_search.js' defer></script>

            <style>
                .item.selected_resource_leftside_menu {
                    background-color: $secondaryColor !important
                }
            </style>
        </head>
        <body class='uk-margin-remove uk-padding-remove'>
        ";

        if(method_exists(self::$resource, 'local_paginate') && self::$resource->local_paginate()) {
            echo "<input type='hidden' id='page_paginator_indicator' value='1'>";
        } else {
            echo "<input type='hidden' id='page_paginator_indicator' value='0'>";
        }

        echo "<input type='hidden' id='authToken' value='". Auth::token() ."'>";

        if(file_exists($_SERVER['DOCUMENT_ROOT'].'/views/header.php')) {
            Presenter::present('header');
        } else {
            Presenter::present('generics.header');
        }


        echo "
            <div class='ui container fluid'>
                <!--div class='uk-text-center'>
                    <span uk-spinner='ratio: 9' id='spinner'></span>
                </div-->

                <div class='ui active loader text waiting' style='display: none;'>". Translation::translate('loading') ."</div>
    
                <div class='uk-position-relative uk-padding-small' id='main-container'>
        ";
    }

    public static function close_tags()
    {

        echo "
                    </div>
                </div>
            </body>

        </html>
        ";

    }

    private static function render() {
        echo self::$generated_code;
    }

    public static function link($resource) {
        $host = app('appUrl', $_SERVER["HTTP_HOST"]);
        $link = str_replace("//", "/", "$host/$resource");
        $requestProtocol = strtolower(explode('/', $_SERVER['SERVER_PROTOCOL'])[0]);
        $link = $requestProtocol."://".$link;
        return $link;
    }

    public static function show(string $resource, array $data = [], $handle_methode_name = "handle")
    {

        self::$handle_methode_name = $handle_methode_name;
        $resource_class = '\\App\\Resources\\'.ucwords($resource);
        $resource_class = str_replace('_', ' ', $resource_class);
        $resource_class = ucwords($resource_class);
        $resource_class = str_replace(' ', '', $resource_class);

            
        self::$resource = new $resource_class();

        if (method_exists(self::$resource, 'get_model_name')) {
            self::$page_title = self::$resource->get_model_name().' | ';
        } else {
            self::$page_title = Translation::translate($resource).' | ';
        }

        self::$page_title = str_replace('_', ' ', self::$page_title);
        self::$page_title = ucfirst(strtolower(self::$page_title));

        $handler = self::$handle_methode_name;
        if (method_exists(self::$resource, $handler)) {
            self::$resource->$handler($data);
        } else {
            Presenter::present("generics.error", [
                "error_info" => Translation::translate('failure'),
                "error_code" => 87,
                "error_description"=>Translation::translate('the_class')."'". get_class(self::$resource) ."' ". Translation::translate('must_implement_method') ." '".self::$handle_methode_name."'"
            ]);
        }
        
        self::render();
        // exit();
    }

    public static function call(string $resource, array $data = [], $handle_methode_name = "handle")
    {

        self::$handle_methode_name = $handle_methode_name;
            
        self::$resource = new $resource();

        $handler = self::$handle_methode_name;
        if (method_exists(self::$resource, $handler)) {
            return self::$resource->$handler($data);
        } else {
            Presenter::present("generics.error", [
                "error_info" => Translation::translate('failure'),
                "error_code" => 87,
                "error_description"=>Translation::translate('the_class')."'". get_class(self::$resource) ."' ". Translation::translate('must_implement_method') ." '".self::$handle_methode_name."'"
            ]);
        }
    }

    public static function routeDetail($route) {
        $route = str_replace($_SERVER['HTTP_ORIGIN'], '', $route);
        $route = preg_replace("#^(/)\.*#", '', $route);
        $route = explode('?', $route)[0];
        $route = explode('/', $route);

        $detail = [
            'resource' => null,
            'action' => null,
            'uid' => null,
        ];

        $detail['resource'] = $route[0] ?? null;
        $detail['action'] = $route[1] ?? null;
        $detail['uid'] = $route[2] ?? null;

        return (Object) $detail;
    }
}

?>
