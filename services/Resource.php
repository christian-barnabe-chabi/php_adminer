<?php

namespace Services;

use Services\Presenter;

use function Lib\plurial_noun;

class Resource
{
    private static $resource;
    private static $page_title;
    private static $generated_code;
    private static $handle_methode_name = "handle";

    public static function load(string $resource, array $data = [], $handle_methode_name = 'handle')
    {

        self::$handle_methode_name = $handle_methode_name;
        $resource_class = '\\App\\Resources\\'.ucwords($resource);
        $resource_class = str_replace('_', ' ', $resource_class);
        $resource_class = ucwords($resource_class);
        $resource_class = str_replace(' ', '', $resource_class);


        if (class_exists($resource_class, false)) {
            
            self::$resource = new $resource_class($data);

            if (method_exists(self::$resource, 'get_model_name')) {
                self::$page_title = self::$resource->get_model_name();
            } else {
                self::$page_title = Translation::translate($resource);
            }
    
            self::$page_title = str_replace('_', ' ', self::$page_title);
            self::$page_title = ucfirst(self::$page_title).' | '.app('app_name');
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
        } else {
            self::open_tags();
            Presenter::present("generics.error", [
                "error_info" => Translation::translate('failure'),
                "error_code" => 90,
                "error_description"=> Translation::translate('resource_not_found')." '".$resource_class."'"
            ]);
            self::close_tags();
        };
        self::close_tags();
        // self::render();
        exit();
    }

    public static function open_tags()
    {

        $title = ucfirst(strtolower(self::$page_title));
        $favicon = app('icon');
        $theme = app('theme', 'light') == 'night' ? "<link rel='stylesheet' href='/assets/css/night.css'>" : "";
        echo "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <link rel='shortcut icon' href='{$favicon}'>
            <title>{$title}</title>
            <script src='/assets/js/jquery.js'></script>
            <link rel='stylesheet' href='/assets/SemanticUI/semantic.min.css'>
            <script src='/assets/SemanticUI/semantic.min.js'></script>
            <script src='/assets/SemanticUI/tablesort.js'></script>
            <link rel='stylesheet' href='/assets/uikit/css/uikit.min.css'>
            <link rel='stylesheet' href='/assets/uikit/css/uikit-rtl.min.css'>
            <script defer src='/assets/uikit/js/uikit-icons.min.js'></script>
            <script defer src='/assets/uikit/js/uikit.min.js'></script>
            <link rel='stylesheet' href='/assets/css/main.css'>
            <script src='/assets/js/chart.js'></script>
            <script src='/assets/js/main.js'></script>
            <script src='/assets/js/paginator.js' defer></script>
            <script src='/assets/js/table_search.js' defer></script>
            <script src='/assets/js/example.js' defer></script>
            $theme
        </head>
        <body class='uk-margin-remove uk-padding-remove'>
        ";
        Presenter::present('header');

        echo "
            <div class='ui container fluid'>
                <div class='uk-text-center'>
                    <span uk-spinner='ratio: 3' id='spinner'></span>
                </div>
    
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
        return "/$resource";
    }

    public static function show(string $resource, array $data = [], $handle_methode_name = "handle")
    {

        self::$handle_methode_name = $handle_methode_name;
        $resource_class = '\\App\\Resources\\'.ucwords($resource);
        $resource_class = str_replace('_', ' ', $resource_class);
        $resource_class = ucwords($resource_class);
        $resource_class = str_replace(' ', '', $resource_class);


        if (class_exists($resource_class, false)) {
            
            self::$resource = new $resource_class();

            if (method_exists(self::$resource, 'get_model_name')) {
                self::$page_title = self::$resource->get_model_name();
            } else {
                self::$page_title = Translation::translate($resource);
            }
    
            self::$page_title = str_replace('_', ' ', self::$page_title);
            self::$page_title = ucfirst(strtolower(self::$page_title)).' | '.app('app_name');

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
        } else {
            self::open_tags();
            Presenter::present("generics.error", [
                "error_info" => Translation::translate('failure'),
                "error_code" => 90,
                "error_description"=> Translation::translate('resource_not_found')." '".$resource_class."'"
            ]);
            self::close_tags();
        };
        
        self::render();
        // exit();
    }

    public static function call(string $resource, array $data = [], $handle_methode_name = "handle")
    {

        self::$handle_methode_name = $handle_methode_name;

        if (class_exists($resource, false)) {
            
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
        } else {
            self::open_tags();
            Presenter::present("generics.error", [
                "error_info" => Translation::translate('failure'),
                "error_code" => 90,
                "error_description"=> Translation::translate('resource_not_found')." '".$resource."'"
            ]);
            self::close_tags();
        };
    }
}

?>
