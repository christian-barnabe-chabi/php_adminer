<?php

namespace App\Scaffolding;

class ColumnAttribute {

    public $type;
    public $css_class;
    public $tooltip;
    public $replacements;
    public $name;
    public $variable_name;
    public $fetch_url;
    public $fetch_method;
    public $displayed_text;
    public $relation_field;
    public $escape_edit;
    public $escape_create;
    public $values;
    public $sub_property;
    public $visible;
    public $labeled;
    public $image;
    public $required;
    public $disabled;
    public $option_image;
    public $callback;

    public function __construct($column = null, $value = null)
    {
        $primary_color = app('primary_color');
        $column = (Object)$column;
        $this->type = isset($column->type) ? $column->type : 'text';
        $this->values = isset($column->values) ? $column->values : null;
        $this->css_class = isset($column->css_class) ? $column->css_class : null;
        $this->tooltip = isset($column->tooltip) ? $column->tooltip : null;
        $this->replacements = isset($column->replacements) ? $column->replacements : null;
        $this->name = isset($column->name) ? $column->name : preg_replace("(\.|_)", " ", $value);
        $this->variable_name = isset($column->variable_name) ? $column->variable_name : $value;
        $this->fetch_url = isset($column->fetch_url) ? $column->fetch_url : null;
        $this->sub_property = isset($column->sub_property) ? $column->sub_property : null;
        $this->escape_edit = isset($column->escape_edit) ? $column->escape_edit : false;
        $this->escape_create = isset($column->escape_create) ? $column->escape_create : false;
        $this->visible = isset($column->visible) ? $column->visible : true;
        $this->fetch_method = isset($column->fetch_method) ? $column->fetch_method : "GET";
        $this->displayed_text = isset($column->displayed_text) ? $column->displayed_text : $value ;
        $this->relation_field = isset($column->relation_field) ? $column->relation_field : 'id';
        $this->labeled = isset($column->labeled) ? "ui label {$primary_color} fluid tiny basic uk-text-center" : '';
        $this->image = isset($column->image) ? $column->image: false;
        $this->required = isset($column->required) && $column->required == false ? '': 'required';
        $this->disabled = isset($column->disabled) && $column->disabled == true ? 'disabled': '';
        $this->option_image = isset($column->option_image) ? $column->option_image: null;
        $this->callback = isset($column->callback) ? $column->callback: null;
    }

}

?>