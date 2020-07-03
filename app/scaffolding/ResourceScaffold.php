<?php

namespace App\Scaffolding;

use Services\Request;

class ResourceScaffold {
    private static $elements = "";

    public static function define($name, $link_target, $icon='') {
        $href = $link_target;

        $match = explode('/', $href);
        $active = self::match_menu($match[0]);

        self::$elements .= "
        <a class='{$active} item' href='/$href'>
            <span>
                <i class='ui {$icon} icon'></i>
                {$name}
            </span>
        </a>";
    }

    public static function render() {
        $primary_color = app('primary_color');
        $inverted = app('colorful') ? 'inverted segment' : '';
        $bg_white = empty($inverted)  ? 'background: whit' : '';
        echo"
            <div class='ui secondary  {$primary_color} {$inverted} vertical menu fixed' id='side-menu' style='overflow: auto; {$bg_white}'>".

                self::$elements

            ."</div>
        ";
    }

    private static function match_menu($name_to_match) : String {
        if(isset(Request::$request->php_admin_resource)) {
            return preg_match("/^".$name_to_match."$/i", Request::$request->php_admin_resource) ? " active " : "";
        }

        return '';
    }

    public static function divider($category = null, $icon = null) {
        $icon = $icon ? "<i class='$icon icon'></i>": '';
        $color = app('colorful') ? 'white' : 'grey';
        self::$elements .= "
            <h6 class='uk-heading-divide' style='color: $color';>
                $icon
                $category
                <div class='ui divider uk-margin-remove'></div>
            </h6>
        ";
        // self::$elements .= "<h6 class='ui horizontal divider tiny header'>
        //         $icon
        //         $category
        //     </h6>
        // ";
    }
}
?>
