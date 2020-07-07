<?php

namespace Lib\form;

use Services\Translation;

class Checkbox {
    private $element;
    private $name;
    public function __construct($name, $default = 'Select ...')
    {
        $this->name = $name;
        $this->element = "
            <select name='{$name}' multiple='' class='ui fluid dropdown search'>
                    <option value=''>$default</option>";
    }

    public function define($text, $value, bool $checked=false) {
        $checked = $checked ? "checked" : '';

        $this->element .= "
                    <option value='{$value}'>{$text}</option>";
    }

    public function render() {
        $this->element .= "
            </select>
        ";
        return $this->element;
    }
}

?>