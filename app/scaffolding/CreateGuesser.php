<?php

namespace App\Scaffolding;

use App\Resources\BaseBlueprint;
use Services\API;
use Services\Auth;
use Lib\form\Checkbox;
use Lib\form\Dropdown;
use Services\Request;
use Services\Translation;

class CreateGuesser {

    public static function render(BaseBlueprint $blueprint) {

        $primary_color = app('primary_color');

        $keys = array_keys($blueprint->get_columns());

        $resource = Request::$request->php_admin_resource;

        $elements = "<form autocomplete='new-password' action='/{$resource}/save' method='POST' enctype='multipart/form-data'>";

            $elements .= "<div class='ui three column stackable grid'>";

                foreach ($keys as $key => $value) {

                    if(in_array($value, $blueprint->get_guarded())) {
                        continue;
                    }

                    if( preg_match("/^field_divider_\d/", $value ) ) {
                        $value = $blueprint->get_columns()[$value];
                        $icon = isset($value['icon']) ? "<i class='{$value['icon']} icon'></i>" : '';
                        $legend = isset($value['legend']) ? $value['legend'] : '';
                        $elements .= "
                            <div class='row one column grid'>
                            <div class='column'>
                                <h6 class='ui horizontal divider tiny header'>
                                    $icon
                                    $legend
                                </h6>
                            </div>
                            </div>";
                        continue;
                    }

                    // $column = (Object)$blueprint->get_columns()[$value];

                    $column = new ColumnAttribute((Object)$blueprint->get_columns()[$value], $value);

                    $column->name = str_replace('_',' ' ,ucfirst($column->name));

                    if($column->escape_create) {
                        continue;
                    }


                    $sub_element = explode('.', $value);
                    // has url to fetch data from
                    if(count($sub_element) > 1 and $column->fetch_url) {

                        // if url to fecth data we will fill the dropdown with not set
                        if(!$column->fetch_url) {
                            continue;
                        }

                        $api = new API();
                        $api->header("Authorization", app('auth_type').' '.Auth::token());
                        $api->callWith(app('base_url').$column->fetch_url, $column->fetch_method);
                        $sub_response = $api->response();


                        // array_shift($sub_element);
                        $last_child = $sub_element[count($sub_element)-1];
                        array_pop($sub_element);

                        $relation = $column->relation_field;
                        $option_image = $column->option_image;
                        
                        array_shift($sub_element);

                        $dropdown = new Dropdown($column->variable_name, null, 'Select '.$column->name, $column->required);
                        foreach ($sub_response as $single_object) {
                            $current_level = $single_object;
                            foreach ($sub_element as $level) {
                                if(isset($current_level->$level)) {
                                    $current_level = $current_level->$level;
                                }
                            }

                            $dropdown->define($current_level->$last_child, $current_level->$relation, $current_level->$option_image);
                        }
                        
                        $elements .= "<div class='column'>";
                            $elements .= "<div class='ui form'>";
                                $elements .= "<div class='field {$column->disabled}'>";


                                $required_indicator = '';
                                if($column->required) {
                                    $required_indicator = "<small class='uk-text-danger'>  (".Translation::translate('filed_required').")</small>";
                                }

                                $elements .= "<label for='".$value."'>".$column->name.$required_indicator."</label>";
                                $elements .= "<div class='ui input'>";
                                    $elements.= $dropdown->render();
                                $elements .= "</div>";

                                $elements .= "</div>";
                            $elements .= "</div>";
                        $elements .= "</div>";
                        continue;
                    }

                    // has values defined and object (dropdown)
                    if( ($column->type == 'object' and !empty($column->values)) and is_array($column->values)) {
                        $dropdown = new Dropdown($column->variable_name, null, 'Select '.$column->name, $column->required);
                        foreach($column->values as $val => $label) {
                            $dropdown->define($label, $val, $column->option_image);
                        }

                        $elements .= "<div class='column'>";
                            $elements .= "<div class='ui form'>";
                                $elements .= "<div class='field'>";

                                $required_indicator = '';
                                if($column->required) {
                                    $required_indicator = "<small class='uk-text-danger'>  (".Translation::translate('filed_required').")</small>";
                                }
                                $elements .= "<label for='".$value."'>".$column->name.$required_indicator."</label>";
                                $elements .= "<div class='ui input'>";
                                    $elements.= $dropdown->render();
                                $elements .= "</div>";

                                $elements .= "</div>";
                            $elements .= "</div>";
                        $elements .= "</div>";
                        continue;
                    }


                    // has values defined and array (checkbox)
                    if (($column->type == 'array' and !empty($column->values)) and is_array($column->values)) {
                        $checkbox = new Checkbox($column->variable_name.'[]');
                        foreach ($column->values as $val => $label) {
                            $checkbox->define($label, $val);
                        }

                        // $column->name = $sub_element[0];
                        $column->name = str_replace('_',' ' ,ucfirst($column->name));

                        $elements .= "<div class='column'>";
                            $elements .= "<div class='ui form'>";
                                $elements .= "<div class='field'>";

                                    $required_indicator = '';
                                    if($column->required) {
                                        $required_indicator = "<small class='uk-text-danger'>  (".Translation::translate('filed_required').")</small>";
                                    }
                                    $elements .= "<label for='".$value."'>".$column->name.$required_indicator."</label>";
                                    $elements .= $checkbox->render();

                                $elements .= "</div>";
                            $elements .= "</div>";
                        $elements .= "</div>";
                        continue;
                    }

                    // is text
                    if($column->type == 'longtext') {

                        $elements .= "<div class='column'>";
                            $elements .= "<div class='ui form'>";
                                $elements .= "<div class='field'>";
    
                                    $required_indicator = '';
                                    if($column->required) {
                                        $required_indicator = "<small class='uk-text-danger'>  (".Translation::translate('filed_required').")</small>";
                                    }
                                    $elements .= "<label for='".$value."'>".$column->name.$required_indicator."</label>";
                                    $elements .= "<div class='ui input'>";
                                        $elements .= "<textarea style='resize: vertical; height: 100px' type='".$column->type."' id='".$value."' name='".$column->variable_name."' placeholder='".str_replace("'", " ", $column->name)."'></textarea>";
                                    $elements .= "</div>";
    
                                $elements .= "</div>";
                            $elements .= "</div>";
                        $elements .= "</div>";
                        continue;

                    }

                    // other types
                    $field_value = null;
                    if($column->type == 'number') {
                        $field_value = 0;
                    }
                    $elements .= "<div class='column'>";
                        $elements .= "<div class='ui form'>";
                            $elements .= "<div class='field'>";

                                $required_indicator = '';
                                if($column->required) {
                                    $required_indicator = "<small class='uk-text-danger'>  (".Translation::translate('filed_required').")</small>";
                                }
                                $elements .= "<label for='".$value."'>".$column->name.$required_indicator."</label>";
                                $elements .= "<div class='ui input'>";
                                    $elements .= "<input {$column->required} value='{$field_value}' autocomplete='new-password' type='".$column->type."' id='".$value."' name='".$column->variable_name."' placeholder='".str_replace("'", " ", $column->name)."'>";
                                $elements .= "</div>";

                            $elements .= "</div>";
                        $elements .= "</div>";
                    $elements .= "</div>";
                }

            $elements .= "</div>";

            $elements .= "<div class='uk-margin'>";
                $elements .= "<button class='ui button $primary_color' type='submit'><i class='ui icon save'></i>". Translation::translate("save") ."</button>";
            $elements .= "</div>";


        $elements .= "</form>";

        echo $elements;

    }
}

?>
