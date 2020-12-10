<?php

namespace Services\Scaffolders\Embeded;

use Abstracts\BaseBlueprint;
use Services\API;
use Services\Auth;
use Lib\form\Checkbox;
use Lib\form\Dropdown;
use Services\Request;
use Services\Scaffolders\ColumnAttribute;
use Services\Translation;

class EditGuesser {

    public static function render(BaseBlueprint $blueprint, $data, string $parent) {

        $resource = Request::$request->php_admin_resource;

        $uid_field = $blueprint->uid_field();
        if(isset($data->$uid_field))
            $uid = $data->$uid_field;
        else
            return;

        $response = $data;

        if(is_array($response)) {
            exit("Multiple data send. Can't handle it");
        }

        $keys = array_keys($blueprint->get_columns());

        $elements = "<div class='embeded_row ui segment'>";

            $elements .= "<input name='php_admin_action' type='hidden' value='update'>";
            $elements .= "<input name='php_admin_uid' type='hidden' value='$uid'>";
            $elements .= "<div class='ui three column stackable grid'>";

                foreach ($keys as $value) {

                    // if not editable
                    if(in_array($value, $blueprint->get_guarded())) {
                        continue;
                    }

                    // divider
                    if(preg_match("/^field_divider_\d/", $value)) {
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

                    $sub_element = explode('.', $value);

                    $val = $sub_element[0];
                    // get the current value of the column and set as value of corresponding input
                    
                    
                    if(isset($response->$val) && !is_array($response->$val) && !is_object($response->$val)) {
                        $old_value = $response->$val;
                    } else {
                        $old_value = null;
                    }
                    


                    $column = (Object)$blueprint->get_columns()[$value];


                    $column = new ColumnAttribute((Object)$blueprint->get_columns()[$value], $value);
                    if(!$column->editable) {
                        continue;
                    }

                    $column->name = str_replace('_',' ' ,ucfirst($column->name));

                    $relation = $column->relation;


                    $sub_element = explode('.', $value);
                    // has url to fetch data from
                    $entry_child = $sub_element[0];
                    
                    if(count($sub_element) > 1 or $column->endpoint) {

                        // if url to fecth data we will fill the dropdown with is not set then show simple input with old value
                        if(!$column->endpoint) {

                            $current = $response;
                            foreach($sub_element as $_key) {
                                if(isset($current->$_key)) {
                                    $current = $current->$_key;
                                }
                            }

                            if(!is_array($current) && !is_object($current))
                                $old_value = $current;
                            else
                                $old_value = null;

                            $elements .= "<div class='column' id='{$column->id}_container'>";
                                $elements .= "<div class='ui form'>";
                                    $elements .= "<div class='field {$column->disabled}'>";

                                        // image
                                        $required_indicator = '';
                                        if($column->required) {
                                            $required_indicator = "<small class='uk-text-danger'>  (".Translation::translate('field required').") (". $column->detail .")</small>";
                                        }

                                        $label  = "<label for='".$value."'>".$column->name.$required_indicator."</label>";
                                        if($column->image) {

                                            if(!preg_match("/(http(s)?:\/\/)?(www.)?.\w{2,6}/i", $old_value)) {
                                                $old_value = preg_replace('/$\//', '', app('baseUrl')) .'/'. preg_replace('/^\//', '', $old_value);  
                                            }

                                            $label  = "<label for='".$value."' uk-lightbox>".$column->name.$required_indicator."<small> (<a href='$old_value'>". Translation::translate('click_to_preview') .")</a></small></label>";
                                        }
                            
                                        // $elements .= "<label for='".$value."'>".$column->name."</label>";
                                        $elements .= $label;
                                        $elements .= "<div class='ui input small'>";
                                        $elements .= "<input class='{$column->class}' id='{$column->id}' maxlength='{$column->length}' {$column->required} autocomplete='new-password' value=". '"' . $old_value . '"' ." 
                                        type='".$column->type."' id='".$value."' 
                                        name='".$column->variable."' 
                                        placeholder=". '"' . $column->name . '"' .">";
                                        $elements .= "</div>";
                                    $elements .= "</div>";
                                $elements .= "</div>";
                            $elements .= "</div>";
                            continue;
                        }

                        $api = new API();
                        $api->header("Authorization", app('authType').' '.Auth::token());
                        $api->callWith(app('baseUrl').$column->endpoint, $column->fetch_method);
                        $sub_response = $api->response();


                        if(count($sub_element) == 1) {
                            array_push($sub_element, $column->relation);
                        }


                        // array_shift($sub_element);
                        $last_child = $sub_element[count($sub_element)-1];
                        $first_child = $sub_element[0];
                        array_pop($sub_element);

                        $relation = $column->relation;
                        $option_image = $column->option_image;
                        

                        if(isset($response->$first_child)) {

                            $var = '';
                            $current = $response;
                            foreach ($sub_element as $var) {
                                if(isset($current->$var)) {
                                    $current = $current->$var;
                                }
                            }

                            if(is_string($old_value) || is_numeric($old_value)) {
                                $old_value = $old_value;
                            } else {
                                $old_value = $current->$relation;
                            }

                        } else {
                            $old_value = null;
                        }
                        
                        array_shift($sub_element);

                        $cell_value = "";
                        if($column->type == 'array') {
                            $checkbox = new Checkbox($column->variable);
                            foreach ($sub_response as $single_object) {
                                $current_level = $single_object;
                                foreach ($sub_element as $level) {
                                    if(isset($current_level->$level)) {
                                        $current_level = $current_level->$level;
                                    }
                                }

                                // TODO get old values
                                $checkbox->define($current_level->$last_child, $current_level->$relation);
                            }

                            $cell_value = $checkbox->render();
                        } else {

                            $dropdown = new Dropdown($column->variable, $old_value, $column->name, $column->required, $column->id, $column->class);
                            foreach ($sub_response as $single_object) {
                                $current_level = $single_object;
                                foreach ($sub_element as $level) {
                                    if(isset($current_level->$level)) {
                                        $current_level = $current_level->$level;
                                    }
                                }
    
                                $dropdown->define($current_level->$last_child, $current_level->$relation, $current_level->$option_image ?? null);
                            }

                            $cell_value = $dropdown->render();
                        }

                        
                        $elements .= "<div class='column' id='{$column->id}_container'>";
                            $elements .= "<div class='ui form'>";
                                $elements .= "<div class='field {$column->disabled}'>";


                                $required_indicator = '';
                                if($column->required) {
                                    $required_indicator = "<small class='uk-text-danger'>  (".Translation::translate('field required').") (". $column->detail .")</small>";
                                }

                                $elements .= "<label for='".$value."'>".$column->name.$required_indicator."</label>";
                                $elements .= "<div class='ui input'>";
                                    $elements.= $cell_value;
                                    // $elements.= $dropdown->render();
                                $elements .= "</div>";

                                $elements .= "</div>";
                            $elements .= "</div>";
                        $elements .= "</div>";
                        continue;
                    }


                    // has values defined and object (dropdown)
                    if( ($column->type == 'object' and !empty($column->values)) and is_array($column->values)) {

                        $dropdown = new Dropdown($column->variable, $old_value, $column->name, $column->required, $column->id, $column->class);
                        foreach($column->values as $val => $label) {
                            $dropdown->define($label, $val, $column->option_image);
                        }

                        $elements .= "<div class='column' id='{$column->id}_container'>";
                            $elements .= "<div class='ui form'>";
                                $elements .= "<div class='field {$column->disabled}'>";


                                $required_indicator = '';
                                if($column->required) {
                                    $required_indicator = "<small class='uk-text-danger'>  (".Translation::translate('field required').") (". $column->detail .")</small>";
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
                        $checkbox = new Checkbox($column->variable.'[]');
                        foreach ($column->values as $val => $label) {
                            $checkbox->define($label, $val);
                        }

                        $column->name = $sub_element[0];
                        $column->name = str_replace('_',' ' ,ucfirst($column->name));

                        $elements .= "<div class='column' id='{$column->id}_container'>";
                            $elements .= "<div class='ui form'>";
                                $elements .= "<div class='field {$column->disabled}'>";

                                    $required_indicator = '';
                                    if($column->required) {
                                        $required_indicator = "<small class='uk-text-danger'>  (".Translation::translate('field required').") (". $column->detail .")</small>";
                                    }

                                    $elements .= "<label for='".$value."'>".$column->name.$required_indicator."</label>";
                                    $elements .= $checkbox->render();

                                $elements .= "</div>";
                            $elements .= "</div>";
                        $elements .= "</div>";
                        continue;
                    }

                    // exit("HERE");
                    // is text
                    if($column->type == 'longtext') {

                        $elements .= "<div class='column' id='{$column->id}_container'>";
                            $elements .= "<div class='ui form'>";
                                $elements .= "<div class='field {$column->disabled}'>";
    
                                    $required_indicator = '';
                                    if($column->required) {
                                        $required_indicator = "<small class='uk-text-danger'>  (".Translation::translate('field required').") (". $column->detail .")</small>";
                                    }

                                    $elements .= "<label for='".$value."'>".$column->name.$required_indicator."</label>";
                                    $elements .= "<div class='ui input mini'>";
                                        $elements .= "<textarea class='{$column->class}' id='{$column->id}' maxlength='{$column->length}' style='resize: vertical; height: 100px' 
                                        type='".$column->type."' id='".$value."' 
                                        name='".$column->variable."' 
                                        placeholder=". '"' .$column->name . '"' . ">{$old_value}</textarea>";
                                    $elements .= "</div>";
    
                                $elements .= "</div>";
                            $elements .= "</div>";
                        $elements .= "</div>";
                        continue;

                    }

                    // image
                    $required_indicator = '';
                    if($column->required) {
                        $required_indicator = "<small class='uk-text-danger'>  (".Translation::translate('field required').") (". $column->detail .")</small>";
                    }

                    $label  = "<label for='".$value."'>".$column->name.$required_indicator."</label>";
                    $image = '';
                    if($column->image) {
                        if(!preg_match("/(http(s)?:\/\/)?(www.)?.\w{2,6}/i", $old_value)) {
                            $old_value = preg_replace('/$\//', '', app('baseUrl')) .'/'. preg_replace('/^\//', '', $old_value);  
                        }
                        $label  = "<label for='".$value."' uk-lightbox>".$column->name.$required_indicator."<small> (<a href='$old_value'>". Translation::translate('click_to_preview') .")</a></small></label>";
                    }

                    if($column->type == 'datetime-local') {
                        $old_value = str_replace(' ', 'T', $old_value);
                    }

                    
                    $elements .= "<div class='column' id='{$column->id}_container'>";
                        $elements .= "<div class='ui form'>";
                            $elements .= "<div class='field {$column->disabled}'>";
                    
                                $elements .= $label; //"<label for='".$value."' uk-lightbox><a href='$old_value'>".$column->name."</a></label>";
                                $elements .= "<div class='ui input small'>";
                                $elements .= "<input class='{$column->class}' id='{$column->id}' maxlength='{$column->length}' {$column->required} autocomplete='new-password' value=". '"' . $old_value . '"' ." 
                                type='".$column->type."' id='".$value."' 
                                name='".$column->variable."' 
                                placeholder=". '"' . $column->name . '"' .">";
                                $elements .= "</div>";
                            $elements .= "</div>";
                        $elements .= "</div>";
                    $elements .= "</div>";
                }

            $elements .= "</div>";


        $elements .= "</div>";

        return $elements;

    }
}

?>
