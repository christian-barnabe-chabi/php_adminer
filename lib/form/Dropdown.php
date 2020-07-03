<?php

namespace Lib\form;

class Dropdown {
    private $element;
    public function __construct($name, $default_value = '', $default_text='', $required = '')
    {
        $this->element = "
        <div class='ui fluid search selection dropdown'>
            <input {$required} autocomplete='new-password' type='hidden' name='{$name}' value='{$default_value}' autocomplete='off'>
            <i class='dropdown icon'></i>
            <div class='default text'>
                {$default_text}
            </div>
            <div class='menu'>
        ";
    }

    public function define($text, $value, $image = null, bool $active=false) {
        $active = $active ? "active" : '';
        $image = $image ? "<img class='ui large avatar image' src='{$image}' style='height: 35px; width: 35px'>" : '';
        $this->element .= "
        <div class='item {active}' data-value='{$value}'>
        {$image}
        {$text}
        </div>";
    }

    public function render() {
        // $this->element .= "<div class='item' data-value=''>NULL</div>";
        $this->element .= "
            </div>
        </div>
        ";
        return $this->element;
    }
}

?>