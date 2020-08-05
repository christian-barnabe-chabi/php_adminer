<?php

namespace App\Scaffolding;

use Abstracts\BaseBlueprint;
use Services\API;
use Services\Auth;
use Services\DateFormater;
use Services\Request;
use Services\Resource;
use Services\Translation;

use function Lib\plurial_noun;

class ShowGuesser {

    private static $blueprint;

    public static function render(BaseBlueprint $blueprint, $uid) {

        self::$blueprint = $blueprint;

        $url = preg_replace('/{id}/', $uid, $blueprint->url());


        $resource = Request::$request->php_admin_resource;

        $api = new API();
        $api->header("Authorization", app('authType').' '.Auth::token());
        $response = $api->get($url)->response();
        
        if(is_array($response)) {
            exit("Multiple data send. Can't handle it");
        }

        $primary_widget = "";
        $widgets_left = "";
        $widgets_right = "";
        $widgets_bottom = "";
        $widgets_top = "";

        foreach ($response as $key => $value) {

            if($blueprint->is_hidden($key)) {
                continue;
            }

            if(isset($blueprint->get_columns()[$key])) {
                $column = new ColumnAttribute($blueprint->get_columns()[$key], $key);
            } else {
                $column = new ColumnAttribute();
            }

            // array // widget bottom
            if(is_array($value)) {
                if(empty($value))
                    continue;

                $widget_content = "";

                $_key = Translation::translate($key);
                $_key = ucfirst(str_replace('_',' ', $_key));
                $_key = plurial_noun(ucfirst(str_replace('_',' ', $_key)));

                $widget_content .= "<div class='ui segment blueprint-show'>";
                $widget_content .= "<h5><i class='ui hand point right icon'></i>$_key</h5>";

                $sample = $value[0];

                // table head
                if(is_object($sample)) {
                    $head = "<tr>";
                    foreach ($sample as $sample_key => $sample_value) {

                        if($blueprint->is_hidden($sample_key)) {
                            continue;
                        }

                        if(!is_array($sample_value) && !is_object($sample_value)) {
                            $sample_key = Translation::translate($sample_key);
                            $sample_key = ucfirst(str_replace('_',' ', $sample_key));
                            $head .= "<th class='collapsing'>$sample_key</th>";
                        }
                    }
                    $head .= "</tr>";
                } else {
                    continue;
                }


                // table data row
                $sub_body = "";
                foreach ($value as $sub_object) {
                    if(is_object($sub_object)) {
                        $sub_body .= "<tr>";
                        foreach($sub_object as $sub_key => $sub_value) {

                            if($blueprint->is_hidden($sub_key)) {
                                continue;
                            }

                            if($callback_return = self::callback($sub_key, $sub_value)) {
                                $sub_key = Translation::translate($sub_key);
                                $sub_key = ucfirst(str_replace('_',' ', $sub_key));
                                $callback_return = preg_replace("/(http(s)?:\/\/.*)/i", "<a href='$1' target='_blank'>$1</a>", $callback_return);
                                $callback_return = preg_replace("/(\w+((\.)?(\w)+)*@\w+(.\w{2,3}){1,})/i", "<a href='mailto:$1' target='_blank'>$1</a>", $callback_return);
                                    $sub_body .= "
                                    <td>
                                        $callback_return
                                    </td>";
                                continue;
                            }

                            if(!is_array($sub_value) && !is_object($sub_value)) {
                                $sub_body .= self::build_cell($column, $sub_value);
                            }
                        }
                        $sub_body .= "</tr>";
                    }
                }

                $widget_content .= self::make_table($head, $sub_body, '500px',);
                $widget_content .= "</div>";

                if(isset(self::$blueprint->get_widgets_positions()[$key])) {

                    switch(self::$blueprint->get_widgets_positions()[$key]) {
                        case 'top':
                            $widgets_top .= $widget_content;
                        break;
                        case 'right':
                            $widgets_right .= $widget_content;
                        break;
                        case 'bottom':
                            $widgets_bottom .= $widget_content;
                        break;
                        case 'left':
                            $widgets_left .= $widget_content;
                        break;
                        default:
                            $widgets_right .= $widget_content;
                        break;
                    }
                } else {
                    $widgets_right .= $widget_content;
                }

                continue;
            }

            // object
            // if(is_object($value) || $column->type == 'object' && !isset($column->values) && !is_string($value)) {
            $widget_content = "";
            if(is_object($value)) {
                    
                
                $_key = Translation::translate($key);
                $_key = ucfirst(str_replace('_',' ', $_key));

                $widget_content .= "<div class='ui segment blueprint-show'>";
                $widget_content .= "<h5><i class='ui hand point right icon'></i>$_key</h5>";

                $sub_body = "";

                // can still comment this block // is defined as object but value is a string
                if(!is_array($value) && !is_object($value)) {

                    if($callback_return = self::callback($key, $value)) {

                        $sub_key = Translation::translate($key);
                        $sub_key = ucfirst(str_replace('_',' ', $sub_key));

                        $sub_body .= "<tr>";
                            $sub_body .= "<td class='collapsing'>$sub_key</td>";
                            $sub_body .= self::build_cell($column, $callback_return);
                        $sub_body .= "</tr>";
                        continue;
                    }

                    if(preg_match('/\w*\.(jpeg|png|bmp|gif|jpg|ico|tiff)/', $value)) {
                        $form = $column->image;
                        $form = in_array($form, ['rounded', 'circular']) ? $form : 'rounded';
        
                        $sub_key = Translation::translate($sub_key);
                        $sub_key = ucfirst(str_replace('_',' ', $sub_key));
                        $sub_body .= "<tr>";
                            $sub_body .= "<td class='collapsing'>$sub_key</td>";
                            $sub_body .= "<td>
                                <span uk-lightbox>
                                    <a href='$value'>
                                        <img src='$value' class='ui rounded image tiny' style='height: 60px; width: auto' alt='$value'>
                                    </a>
                                </span>
                            </td>";
                        $sub_body .= "</tr>";
                        continue;
                    }

                    $value = preg_replace("/(http(s)?:\/\/.*)/i", "<a href='$1' target='_blank'>$1</a>", $value);
                    $value = preg_replace("/(\w+((\.)?(\w)+)*@\w+(.\w{2,3}){1,})/i", "<a href='mailto:$1' target='_blank'>$1</a>", $value);
                    $sub_body .= "<tr>";
                        $sub_body .= "<td> {$value}</td>";
                    $sub_body .= "</tr>";
                }

                foreach($value as $sub_key => $sub_value) {


                    if($blueprint->is_hidden($sub_key)) {
                        continue;
                    }

                    if(!is_array($sub_value) && !is_object($sub_value)) {

                        if($callback_return = self::callback($sub_key, $sub_value)) {
                            $sub_key = Translation::translate($sub_key);
                            $sub_key = ucfirst(str_replace('_',' ', $sub_key));

                            // var_dump($callback_return);

                            $sub_body .= "<tr>";
                                $sub_body .= "<td class='collapsing'>$sub_key</td>";
                                $sub_body .= self::build_cell($column, $callback_return);
                            $sub_body .= "</tr>";
                            continue;
                        }

                        if(preg_match('/\w*\.(jpeg|png|bmp|gif|jpg|ico|tiff)/', $sub_value)) {
                            $form = $column->image;
                            $form = in_array($form, ['rounded', 'circular']) ? $form : 'rounded';
            
                            $sub_key = Translation::translate($sub_key);
                            $sub_key = ucfirst(str_replace('_',' ', $sub_key));
                            $sub_body .= "<tr>";
                                $sub_body .= "<td class='collapsing'>$sub_key</td>";
                                $sub_body .= "<td>
                                    <span uk-lightbox>
                                        <a href='$sub_value'>
                                            <img src='$sub_value' class='ui rounded image tiny' style='height: 60px; width: auto' alt='$sub_value'>
                                        </a>
                                    </span>
                                </td>";
                            $sub_body .= "</tr>";
                            continue;
                        }

                        

                        $sub_key = Translation::translate($sub_key);
                        $sub_key = ucfirst(str_replace('_',' ', $sub_key));

                        $sub_body .= "<tr>";
                            $sub_body .= "<td class='collapsing'>$sub_key</td>";
                            $sub_value = preg_replace("/(http(s)?:\/\/.*)/i", "<a href='$1' target='_blank'>$1</a>", $sub_value);
                            $sub_value = preg_replace("/(\w+((\.)?(\w)+)*@\w+(.\w{2,3}){1,})/i", "<a href='mailto:$1' target='_blank'>$1</a>", $sub_value);
                            $sub_value = DateFormater::format(app('dateFormat'), $sub_value);
                            $sub_body .= "<td>$sub_value</td>";
                        $sub_body .= "</tr>";
                    }
                }

                // $head = "<tr><th class='collapsing'>". Translation::translate('key') ."</th><th>". Translation::translate('value') ."</th></tr>";
                $head= '';
                $widget_content .= self::make_table($head, $sub_body, '500px', false);
                $widget_content .= "</div>";

                if(isset(self::$blueprint->get_widgets_positions()[$key])) {

                    switch(self::$blueprint->get_widgets_positions()[$key]) {
                        case 'top':
                            $widgets_top .= $widget_content;
                        break;
                        case 'right':
                            $widgets_right .= $widget_content;
                        break;
                        case 'bottom':
                            $widgets_bottom .= $widget_content;
                        break;
                        case 'left':
                            $widgets_left .= $widget_content;
                        break;
                        default:
                            $widgets_right .= $widget_content;
                        break;
                    }
                } else {
                    $widgets_right .= $widget_content;
                }
                
                continue;
            }

            
            // TODO callback
            if($callback_return = self::callback($key, $value)) {
                $_key = Translation::translate($key);
                $primary_widget .= "<tr>";
                    $primary_widget .= "<td class='collapsing'>$_key</td>";
                    $primary_widget .= self::build_cell($column, $callback_return);
                $primary_widget .= "</tr>";
                continue;
            }
            
            
            $labeled = str_replace('fluid','', $column->labeled);

            if(isset($column->replacements[$value])) {
                $value = $column->replacements[$value];
            } else {
                if(isset($column->values)) {
                    $value = $column->values[$value];
                } 
            }

            $key = Translation::translate($key);

            $_key = ucfirst(str_replace('_',' ', $key));
            $primary_widget .= "<tr>";
                $primary_widget .= "<td class='collapsing'>$_key</td>";
                $primary_widget .= self::build_cell($column, $value, null, null, $labeled);
            $primary_widget .= "</tr>";

        }

        // delete create button if needed
        $delete_element = "";
        $delete_modal_confirm = "";
        if($blueprint->deleteable()) {
            $delete_element = "
                <span 
                    onclick=\"
                        $('#confirm_delete').modal({
                            transition: 'fly',
                        }).modal('show')\"
                    
                    class='ui button mini orange basic' >
                    <i class=' ui icon trash'></i>
                    ". Translation::translate("delete") ."
                </span>
            ";

            $delete_modal_confirm = "<div class='ui modal mini' id='confirm_delete'>";
                $delete_modal_confirm .= "<div class='header'>";
                    $delete_modal_confirm .= Translation::translate('are_you_sure_to_delete');
                $delete_modal_confirm .= "</div>";
    
                $delete_modal_confirm .= "<form method='POST' class='actions' action='/{$resource}'>";
                    $delete_modal_confirm .= "<input name='php_admin_action' type='hidden' value='delete'>";
                    $delete_modal_confirm .= "<input name='php_admin_uid' type='hidden' value='$uid'>";
                    $delete_modal_confirm .= "<button type='submit' class='ui button small orange'>". Translation::translate('delete') ."</button>";
                    $delete_modal_confirm .= "<button type='button' class='ui button small olive deny'>". Translation::translate('cancel') ."</button>";
                $delete_modal_confirm .= "</form>";
            $delete_modal_confirm .= "</div>";
        }


        // edit create button if needed
        $edit_element = "";
        $update_modal_forms = "";
        if($blueprint->editable()) {
            $modal_data_id = substr(md5($uid), 0, 10);
            $edit_element = "
                <span onclick=\"
                    $('#$modal_data_id').modal({
                        transition: 'drop',
                    }).modal('show')\"
                href='/{$resource}/edit/{$uid}' class='ui button mini blue basic' >
                    <i class=' ui icon pencil'></i>
                    ". Translation::translate("edit") ."
                </span>
            ";

            // edit form for update
            $class = get_class(self::$blueprint);
            $update_modal_forms = "<div class='ui large modal' id='$modal_data_id'>";
                $update_modal_forms .= "<div class='header'>";
                    $update_modal_forms .= $blueprint->get_name_singular() ." > ". Translation::translate('update');
                $update_modal_forms .= "</div>";

                $update_modal_forms .= "<div class='content scrolling'>";
                    $update_modal_forms .= Resource::call($class, (Array)$response, 'edit');
                $update_modal_forms .= "</div>";
            $update_modal_forms .= "</div>";
        }

        // export create button if needed
        $export_element = "";
        if($blueprint->exportable()) {
            $export_element = "
                <form action='/{$resource}' method='POST' class='uk-display-inline'>
                    <input type='hidden' name='php_admin_export'>
                    <button name='selected_id[]' value='{$uid}' type='submit' class='ui button mini teal basic' >
                        <i class='ui icon save'></i>
                        ". Translation::translate("export") ."
                    </button>
                </form>
            ";
        }

        // scaffolding
        $primary_color = app('primaryColor');
        $element = "";
        if($blueprint->editable() OR $blueprint->deleteable() OR $blueprint->exportable()) {
            $element .= "
                <div class='ui segment $primary_color'>
                    <div class='ui two column grid stackable'>
                        <div class='column'>
                            
                        </div>
                        <div class='column uk-text-right'>
    
                            {$edit_element}
                            {$delete_element}
                            {$export_element}
    
                        </div>
                    </div>
                </div>
            ";
        }

        
        // widget_top
        if(strlen($widgets_top)) {
            $element .= "<div class='ui one column grid'>";
                $element .= "<div class='column'>";
                    $element .= $widgets_top;
                $element .= "</div>";
            $element .= "</div>";
        }

        $element .= "<div class='ui two column grid'>";

            $element .= "<div class='column nine wide'>";
                    $element .= "<div class='ui segment blueprint-show'>";
                    $element .= "<h5><i class='ui hand point right icon'></i>". Translation::translate('direct_attached_data') ."</h5>";
                    $head = ''; //"<tr><th class='collapsing'>". Translation::translate('key') ."</th><th>". Translation::translate('value') ."</th></tr>";
                    $element .= self::make_table($head, $primary_widget, '800px', false);
                    $element .= "</div>";
                    $element .= $widgets_left;
            $element .= "</div>";

            $element .= "<div class='column seven wide'>";
                $element .= $widgets_right;
            $element .= "</div>";

        $element .= "</div>";

        // widget bottom
        $element .= "<div class='ui one column grid'>";
            $element .= "<div class='column'>";
                $element .= $widgets_bottom;
            $element .= "</div>";
        $element .= "</div>";

        // echo $element;
        $element .= $update_modal_forms;
        $element .= $delete_modal_confirm;
        return $element;
    }

    private static function make_table($head, $body, string $max_height = 'auto', $single_line = true) {
        $primary_color = app('primaryColor');
        $element = "";

        $single_line = $single_line ? ' single line ' : '';

        $element .= "<div class='uk-overflow-auto' style='max-height:$max_height'>";
        // $element .= "<table id='show_tables' class='ui $primary_color selectable celled striped single line table compact'>";
            $element .= "<table id='show_tables' class='ui $primary_color selectable celled striped {$single_line} table compact'>";

                $element .= "<thead>";
                    $element .= $head; // tr.th
                $element .= "</thead>";

                $element .= "<tbody>";
                    $element .= $body; // tr.td
                $element .= "</tbody>";

            $element .= "</table>";
        $element .= "</div>";

        return $element;
    }

    private static function callback($column, $value) {
        if(isset(self::$blueprint->callbacks()[$column])) {
            $callback = self::$blueprint->callbacks()[$column];

            if(method_exists(self::$blueprint, $callback)) {
                
                $build_result = self::$blueprint->$callback($value);
                return $build_result;
            }
        }

        return null;
    }

    private static function build_cell(ColumnAttribute $column, $cell_value, $cell_link = null, $tooltip = null, $labeled = null) {
        $elements = "";
        if(preg_match('/\w*\.(jpeg|png|bmp|gif|jpg|ico|tiff)/', $cell_value)) {
            $form = '';       
            if(isset($column->image)) {
                $form = $column->image;
            }

            $form = in_array($form, ['rounded', 'circular']) ? $form : 'rounded';
            $elements .=
            "<td class='' uk-lightbox>
                <a href='$cell_value'>
                <img {$tooltip} src='$cell_value' class='ui $form image avata tiny' style='height: 60px; width: auto' alt=''>
                </a>
            </td>";
            return $elements;
        }

        // link

        $cell_value = DateFormater::format(app('dateFormat'), $cell_value);

        $re1 = '/(http(s)?:\/\/)(www.)?((\w|-|_)+\.\w{2,5}(\/(\w|\?|\=|-|_)*)*)/i';
        $re2 = '/(\w+((\.|-|_)?(\w)+)*@(\w+|-|_|\.)+\.\w{2,5})/i';
        if(preg_match($re1, $cell_value) || preg_match($re2, $cell_value)) {
            $cell_value = preg_replace($re1, "<a href='$0' target='_blank'>$0</a>", $cell_value);
            $cell_value = preg_replace($re2, "<a href='mailto:$0' target='_blank'>$0</a>", $cell_value);
            $cell_link = '';

            $elements .=
            "<td {$cell_link} class=''>
                <span {$tooltip} {$labeled}>".
                    $cell_value
                ."</span>
            </td>";

            return $elements;
        }

        if(preg_match("/(\d\d:\d\d):\d\d/", $cell_value, $matches)) {
            $cell_value = $matches[1];
        }

        $elements .=
        "<td {$cell_link} class=''>
            <span {$tooltip} {$labeled}>".
                $cell_value
            ."</span>
        </td>";

        return $elements;
    }
}

?>