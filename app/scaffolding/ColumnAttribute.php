<?php

namespace App\Scaffolding;

class ColumnAttribute {

    public $type;
    public $tooltip;
    public $replacements;
    public $name;
    public $variable;
    public $endpoint;
    public $fetch_method;
    public $relation;
    public $editable;
    public $createable;
    public $values;
    public $visible;
    public $labeled;
    public $image;
    public $required;
    public $disabled;
    public $option_image;
    public $callback;
    public $length;
    public $id;
    public $class;

    public function __construct($column = null, $value = null)
    {
        $primary_color = app('primary_color');
        $column = (Object)$column;
        $this->type = isset($column->type) ? $column->type : 'text';
        $this->values = isset($column->values) ? $column->values : null;
        $this->tooltip = isset($column->tooltip) ? $column->tooltip : null;
        $this->replacements = isset($column->replacements) ? $column->replacements : null;
        $this->name = isset($column->name) ? $column->name : preg_replace("(\.|_)", " ", $value);
        $this->variable = isset($column->variable) ? $column->variable : $value;
        $this->endpoint = isset($column->endpoint) ? $column->endpoint : null;
        $this->fetch_method = isset($column->method) ? $column->method : "GET";
        $this->editable = isset($column->editable) ? $column->editable : true;
        $this->createable = isset($column->createable) ? $column->createable : true;
        $this->visible = isset($column->visible) ? $column->visible : true;
        $this->relation = isset($column->relation) ? $column->relation : 'id';
        $this->labeled = isset($column->labeled) ? "ui label {$primary_color} fluid tiny basic uk-text-center" : '';
        $this->image = isset($column->image) ? $column->image: false;
        $this->required = isset($column->required) && $column->required == false ? '': 'required';
        $this->disabled = isset($column->disabled) && $column->disabled == true ? 'disabled': '';
        $this->option_image = isset($column->option_image) ? $column->option_image : null;
        $this->callback = isset($column->callback) ? $column->callback: null;
        $this->length = isset($column->length) ? $column->length: 128;
        $this->id = isset($column->id) ? $column->id: '';
        $this->class = isset($column->class) ? $column->class: '';
    }

}

?>