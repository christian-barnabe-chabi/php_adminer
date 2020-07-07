<?php

namespace App\Scaffolding;

use App\Resources\BaseBlueprint;
use Services\API;
use Services\Auth;
use Lib\form\Checkbox;
use Lib\form\Dropdown;
use Services\Request;
use Services\Translation;

class EditGuesser {

    public static function render(BaseBlueprint $blueprint, $uid) {

        $primary_color = app('primary_color');

        $url = preg_replace('/{id}/', $uid, $blueprint->url());

        $resource = Request::$request->php_admin_resource;

        // fetch data
        $api = new API();
        $api->header("Authorization", app('auth_type').' '.Auth::token());
        $response = $api->get($url)->response();

        // var_dump($response);
        // exit();

        if(is_array($response)) {
            exit("Multiple data send. Can't handle it");
        }

        $keys = array_keys($blueprint->get_columns());

        $elements = "<form autocomplete='new-password' action='/{$resource}/update/{$uid}' method='POST' enctype='multipart/form-data'>";

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

                            $elements .= "<div class='column'>";
                                $elements .= "<div class='ui form'>";
                                    $elements .= "<div class='field {$column->disabled}'>";
                            
                                        $elements .= "<label for='".$value."'>".$column->name."</label>";
                                        $elements .= "<div class='ui input'>";
                                        $elements .= "<input {$column->required} autocomplete='new-password' value=". '"' . $old_value . '"' ." 
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
                        $api->header("Authorization", app('auth_type').' '.Auth::token());
                        $api->callWith(app('base_url').$column->endpoint, $column->fetch_method);
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
                            
                            $dropdown = new Dropdown($column->variable, $old_value, 'Select '.$column->name, $column->required);
                            foreach ($sub_response as $single_object) {
                                $current_level = $single_object;
                                foreach ($sub_element as $level) {
                                    if(isset($current_level->$level)) {
                                        $current_level = $current_level->$level;
                                    }
                                }
    
                                $dropdown->define($current_level->$last_child, $current_level->$relation, $current_level->$option_image);
                            }

                            $cell_value = $dropdown->render();
                        }

                        
                        $elements .= "<div class='column'>";
                            $elements .= "<div class='ui form'>";
                                $elements .= "<div class='field {$column->disabled}'>";


                                $required_indicator = '';
                                if($column->required) {
                                    $required_indicator = "<small class='uk-text-danger'>  (".Translation::translate('field required').")</small>";
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


                    // exit("HERE");
                    // is text
                    if($column->type == 'longtext') {

                        $elements .= "<div class='column'>";
                            $elements .= "<div class='ui form'>";
                                $elements .= "<div class='field {$column->disabled}'>";
    
                                    $required_indicator = '';
                                    if($column->required) {
                                        $required_indicator = "<small class='uk-text-danger'>  (".Translation::translate('field required').")</small>";
                                    }

                                    $elements .= "<label for='".$value."'>".$column->name.$required_indicator."</label>";
                                    $elements .= "<div class='ui input'>";
                                        $elements .= "<textarea style='resize: vertical; height: 100px' 
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
                        $required_indicator = "<small class='uk-text-danger'>  (".Translation::translate('field required').")</small>";
                    }

                    $label  = "<label for='".$value."'>".$column->name.$required_indicator."</label>";
                    $image = '';
                    if($column->image) {
                        $form = $column->image;
                        $form = in_array($form, ['rounded', 'circular']) ? $form : 'rounded';  
                        $old_value = preg_replace('/$\//', '', app('origin')) .'/'. preg_replace('/^\//', '', $old_value);  
                        $label  = "<label for='".$value."' uk-lightbox>".$column->name.$required_indicator."<small> (<a href='$old_value'>". Translation::translate('click_to_preview') .")</a></small></label>";
                    }

                    if($column->type == 'datetime-local') {
                        $old_value = str_replace(' ', 'T', $old_value);
                    }

                    
                    $elements .= "<div class='column'>";
                        $elements .= "<div class='ui form'>";
                            $elements .= "<div class='field {$column->disabled}'>";
                    
                                $elements .= $label; //"<label for='".$value."' uk-lightbox><a href='$old_value'>".$column->name."</a></label>";
                                $elements .= "<div class='ui input'>";
                                $elements .= "<input {$column->required} autocomplete='new-password' value=". '"' . $old_value . '"' ." 
                                type='".$column->type."' id='".$value."' 
                                name='".$column->variable."' 
                                placeholder=". '"' . $column->name . '"' .">";
                                $elements .= "</div>";
                            $elements .= "</div>";
                        $elements .= "</div>";
                    $elements .= "</div>";
                }

            $elements .= "</div>";

            $elements .= "<div class='uk-margin'>";
                $elements .= "<button class='ui button $primary_color' type='submit'><i class='ui icon save'></i>". Translation::translate("update") ."</button>";
            $elements .= "</div>";


        $elements .= "</form>";

        echo $elements;

    }
}

?>
