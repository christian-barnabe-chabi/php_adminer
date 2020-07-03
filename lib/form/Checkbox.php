<?php

namespace Lib\form;

class Checkbox {
    private $element;
    private $name;
    public function __construct($name)
    {
        $this->name = $name;
        $this->element = "
        <div class=''>
            <select name='{$name}' multiple='' class='ui fluid dropdown search'>
            <option value=''>Skills</option>
        ";
    }

    public function define($text, $value, bool $checked=false) {
        $checked = $checked ? "checked" : '';

        // $this->element .= "
        //     <label>
        //         <input id='{$value}' class='uk-checkbox' type='checkbox' {$selected} value='{$value}' name={$this->name}>
        //         {$text}
        //     </label>
        // ";

        $this->element .= "<option value='{$value}'>{$text}</option>";

        // $this->element .= "
        // <div class='ui checkbox uk-margin-small'>
        //     <input id='{$value}' class='uk-checkbox' type='checkbox' {$checked} value='{$value}' name={$this->name}>
        //     <label for='{$value}'>{$text}</label>
        // </div>
        // ";
    }

    public function render() {
        $this->element .= "
            </select>
        </div>
        ";
        return $this->element;
    }
}

?>