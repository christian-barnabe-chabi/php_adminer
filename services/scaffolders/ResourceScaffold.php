<?php

namespace Services\Scaffolders;

use Services\Request;

class ResourceScaffold {
    private static $elements = "";

    public static function define($name, $link_target, $icon='') {
        $href = $link_target;

        $match = explode('/', $href);
        $active = self::match_menu($match[0]);
        $class = $active ? ' active ' : '';
        $secondaryColor = app('secondaryColor', 'rgba(0, 172, 29, 0.979)');

        if($active)
            self::$elements .= "
            <a class='{$class} item' style='background-color: $secondaryColor !important' href='/$href'>
                <span>
                    <i class='ui {$icon} icon'></i>
                    {$name}
                </span>
            </a>";
        else
            self::$elements .= "
            <a class='{$class} item' href='/$href'>
                <span>
                    <i class='ui {$icon} icon'></i>
                    {$name}
                </span>
            </a>";
    }

    public static function render() {
        $primary_color = app('primaryColor');
        $inverted = app('colorful') ? 'inverted segment' : '';
        $bg_white = empty($inverted)  ? 'background: white' : '';
        echo"
            <div class='ui secondary segment {$primary_color} {$inverted} vertical menu fixed' id='side-menu' style='overflow: auto; {$bg_white}; padding: 5px 5px; box-shadow: 3px 0px 3px rgba(0, 0, 0, 0.2)'>".

                self::$elements

            ."</div>
        ";
    }

    private static function match_menu($name_to_match) : bool {
        if(isset(Request::$request->php_admin_resource)) {
            return preg_match("/^".$name_to_match."$/i", Request::$request->php_admin_resource) ? true : false;
        }

        return false;
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
