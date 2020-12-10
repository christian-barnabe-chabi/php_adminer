<?php

namespace Services\Scaffolders;

use Abstracts\BaseBlueprint;
use Exception;
use Services\Request;

use function Lib\plurial_noun;

class CsvExportGuesser {
    private static $objects;
    private static $blueprint;
    private static $columns;
    private static $table_columns;

    

    public static function render(BaseBlueprint $blueprint, $objects) {
        
        // BEGIN

        if(empty($blueprint->get_columns())) return null;

        $primary_color = app('primaryColor');

        self::$blueprint = $blueprint;
        self::$columns = $blueprint->get_columns();
        self::$objects = $objects;
        self::$table_columns = array_keys(self::$columns);

        $route = Request::$request->php_admin_resource;


        $class = get_class(self::$blueprint);


        $update_modal_forms = "";
        $elements = "";

        // HEAD
        // row begining
        foreach(self::$table_columns as $table_head) {
            $column = new ColumnAttribute(self::$columns[$table_head], $table_head);

            if($column->visible == false) {
                continue;
            }

            if(preg_match("/^field_divider_\d/", $table_head)) {
                continue;
            }

            // TODO check if the column is an array or an object

            $sub_element = explode('.', $table_head);

            // First line - Columns title
            if(count($sub_element) > 1 && $column->name == $table_head) {
                $elements .= self::csv_format(ucfirst(  str_replace('_', ' ', $sub_element[0]) )).', ';
            } else {
                $elements .= self::csv_format(ucfirst(  str_replace('_', ' ', $column->name))). ', ';
            }

        }

        $elements .= "\r\n";

        // row ended
                    

        // table body now
        // for each object in objects fetched
        foreach(self::$objects as $obj) {
            $uid_field = $blueprint->uid_field();
            $uid = $obj->$uid_field;
            Request::$request->uid = $uid;
            // for each column in rows
            foreach(self::$table_columns as $column_name) {

                $cell_values = "";
                $cell_value = "";

                $column = new ColumnAttribute(self::$columns[$column_name], $column_name);

                if($column->visible == false) {
                    continue;
                }

                // escape dividers
                if(preg_match("/^field_divider_\d/", $column_name)) {
                    continue;
                }

                $replacements = $column->replacements;

                // categoty.name becomes category, name
                $sub_element = explode('.', $column_name);

                //column is an array or object
                if(count($sub_element) > 1){
                    // todo
                    $current_level = $obj;
                    foreach ($sub_element as $level) {
                        if(isset($current_level->$level)) {
                            $current_level = $current_level->$level;
                        }
                    }
                    // final value
                    // TODO handle object array
                    
                    if(!is_array($current_level) && !is_object(($current_level))) {

                        $current_level = $replacements[$current_level] ?? $current_level;

                        $elements .=  self::csv_format($current_level).', ';

                        continue;
                    }
                    else {
                        // TODO check it again
                        $elements .= ',';
                        continue;
                    }

                    // TODO handle iterable data #isArray
                    // else {

                    // }
                }

                // column is an object and no sub_property set or set explicitly
                if (is_object($obj->$column_name)) {
                    $sub_property = $column->relation;

                    if (isset($obj->$column_name->$sub_property)) {
                        $cell_value = $obj->$column_name->$sub_property;
                    } else {
                        $cell_value = 'error';
                    };
                    

                    $cell_value = $replacements[$cell_value] ?? $cell_value;

                    $elements .= self::csv_format($cell_value).',';

                    continue;
                }

                // TODO implement callback
                // column is an array and no sub_property set or set explicitly
                if (is_array($obj->$column_name)) {
                    if (empty($obj->$column_name)) {
                        $cell_values .= " , ";
                    } else {
                        foreach ($obj->$column_name as $key => $value) {
                            //TODO

                            $val = ((Array)$value)[$sub_property];
                            $sub_property = $column->relation;
                            if (isset($value->$sub_property)) {
                                $cell_values .= $val;
                            } else {
                                $cell_values .= "error";
                            }
                        }
                    }

                    // TODO check for replacements
                    $elements .= self::csv_format($cell_values).',';

                    $cell_values = "";

                    continue;
                }

                $cell_value = $obj->$column_name ?? '';

            
                if(isset($replacements[$cell_value])) {
                    $cell_value = $replacements[$cell_value];
                } else {
                    if(isset($column->values)) {
                        $cell_value = $column->values[$obj->$column_name];
                    } else {
                        $cell_value = $obj->$column_name ?? '';
                    }
                }

                $elements .= self::csv_format($cell_value).',';

            }

            $elements .= "\r\n";

            continue;
        }

        $elements .= "\r\n";
        $elements = preg_replace("/(,\s\r\n)/", "\n", $elements);
        $elements = preg_replace("/(\s\r\n$)/", "", $elements);

        if(!(\is_dir("temp"))) {
            mkdir("temp");
        }

        $exporting = explode( "\\" ,get_class($blueprint));
        $exporting = plurial_noun(strtolower($exporting[count($exporting)-1]));
        $filename = 'export_'.$exporting.'_'.date("Y_m_d:h_i_s").'.csv';
        $path = 'temp/'.$filename;
        if($file = fopen($path, 'w')) {
            try {
                fwrite($file, $elements);
                fclose($file);
            } catch(Exception $e) {
                echo $e->getMessage();
            }
        }

        return ['file_content'=>$elements, 'file_link'=>"<a target='_blank' class=' uk-link uk-link-heading external-link' href='/$path'><i class='ui icon file excel large'></i> ".$filename."</a>"];

    }

    // END

    private static function csv_format(string $string) {
        if(preg_match("/.,./", $string) || strtolower($string) == 'id') {
            $string = '"'.$string.'"';
        }
        return $string;
    }
}

// {
//     if(!(\is_dir("temp"))) {
//         mkdir("temp");
//     }

//     $exporting = explode( "\\" ,get_class($blueprint));
//     $exporting = plurial_noun(strtolower($exporting[count($exporting)-1]));
//     $filename = 'export_'.$exporting.'_'.date("Y_m_d:h_i_s").'.csv';
//     $path = 'temp/'.$filename;
//     if($file = fopen($path, 'w')) {
//         try {
//             fwrite($file, $elements);
//             fclose($file);
//         } catch(Exception $e) {
//             echo $e->getMessage();
//         }
//     }

//     return (['file_content'=>$elements, 'file_link'=>"<a target='_blank' class='uk-link uk-link-heading external-link' href='/$path'><i class='ui icon file excel large'></i> ".$filename."</a>"]);
// }

?>