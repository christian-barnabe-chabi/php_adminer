<?php

namespace App\Scaffolding;

use App\Resources\BaseBlueprint;
use Exception;
use Services\Request;

use function Lib\plurial_noun;

class CsvExportGuesser {
    private static $objects;
    private static $blueprint;
    private static $columns;
    private static $table_columns;

    public static function render(BaseBlueprint $blueprint, $objects) {

        if(empty($blueprint->get_columns())) return null;

        self::$blueprint = $blueprint;
        self::$columns = $blueprint->get_columns();
        self::$objects = $objects;
        self::$table_columns = array_keys(self::$columns);

        // add table heads
        $elements = "";
        foreach(self::$table_columns as $table_head) {
            $column = new ColumnAttribute(self::$columns[$table_head], $table_head);

            if($column->visible == false) {
                continue;
            }

            // divider
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

        // for each object in objects fetched
        foreach(self::$objects as $obj) {
            // for each column in columns
            foreach(self::$table_columns as $column_name) {

                $cell_values = "";
                $cell_value = "";

                $column = new ColumnAttribute(self::$columns[$column_name], $column_name);

                if($column->visible == false) {
                    continue;
                }

                $replacements = $column->replacements;

                // categoty.name becomes category, name
                $sub_element = explode('.', $column_name);

                //column is an object or object and sub_property set
                if(count($sub_element) > 1){
                    $entry_child = $sub_element[0];
                    $sub_property = isset($sub_element[1]) ? $sub_element[1] : 'id';

                    // TODO check if it works
                    $sub_property = $column->relation;

                    // array (list as it is - checkbox)
                    if(is_array($obj->$entry_child)) {
                        foreach ($obj->$entry_child as $key => $value) {
                            // TODO
                            if(isset($value->$sub_property)) {
                                $cell_values .= ucfirst( ((Array)$value)[$sub_element[1]] );
                            }
                        }
                        $elements .= $cell_values.', ';
                        continue;
                    }

                    // object (dropdown)
                    if(isset($obj->$entry_child->$sub_property)) {
                        $cell_value = $obj->$entry_child->$sub_property;
                    }
                    else {
                        $cell_value = ' ';
                    }

                    $elements .= $cell_values.', ';
                    continue;
                }

                if (isset($obj->$column_name)) {
                    // column is an object and no sub_property set or set explicitly
                    if (is_object($obj->$column_name)) {
                        
                        $sub_property = $column->relation;
                        
                        if (isset($obj->$column_name->$sub_property)) {
                            $cell_value .= $obj->$column_name->$sub_property;
                        } else {
                            $cell_value .= 'null';
                        };

                        $elements .= $cell_value.', ';
                        continue;
                    }

                    // column is an array and no sub_property set or set explicitly
                    if (is_array($obj->$column_name)) {
                        if (empty($obj->$column_name)) {
                            $cell_values .= " ";
                        } else {
                            foreach ($obj->$column_name as $key => $value) {
                                //TODO
                                $sub_property = $column->relation;
                                if (isset($value->$sub_property)) {
                                    $cell_values .= ucfirst(((Array)$value)[$sub_property]);
                                } else {
                                    $cell_values .= 'error';
                                }
                            }
                        }

                        $elements .= $cell_values.', ';
                        continue;
                    }
                } else {
                    $elements .= $cell_value = ', ';
                    continue;
                }

                if(isset($obj->$column_name)) {

                    $cell_value = isset($replacements[$obj->$column_name]) ? $replacements[$obj->$column_name] : ucfirst( $obj->$column_name );

                } else {
                    $cell_value = ', ';
                }

                $elements .= $cell_value.', ';
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

        return ['file_content'=>$elements, 'file_link'=>"<a class=' uk-link uk-link-heading' href='/$path'><i class='ui icon file excel large'></i> ".$filename."</a>"];
    }

    private static function csv_format(string $string) {
        if(preg_match("/.,./", $string) || strtolower($string) == 'id') {
            $string = '"'.$string.'"';
        }
        return $string;
    }
}

?>