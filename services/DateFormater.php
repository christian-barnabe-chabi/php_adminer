<?php

namespace Services;

class DateFormater {

    public static function format(string $format, $date) {
        $re = "/(\d{4}-\d{1,2}-\d{1,2})(T|UTC)?(\d{1,2}:\d{1,2}:\d{1,2})?(.\d{1,}(Z)?)?/i";
        if(preg_match_all($re, $date, $matches)) {
            $date = $matches[1][0];

            if(isset($matches[3][0])) {
                $date .= " ". $matches[3][0];
            }

            $date_mk = ( strtotime($date) );
            $date = date($format, $date_mk);
        }

        return $date;
    }

}

?>