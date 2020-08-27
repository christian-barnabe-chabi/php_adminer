<?php
namespace Services;

class Translation {

    public static function translate(string $word) {
        if(!isset($_SESSION['transalation'])) {

            $file = fopen($_SERVER['DOCUMENT_ROOT']."/.translations.json", "r");
            $file_lines = '';
            while($line = fgets($file)) {
                $file_lines .=  $line;
            }

            $file_lines = str_replace("\n", '', $file_lines);

            $_SESSION['transalation'] = json_decode($file_lines);

        }
        $translation = trim($word);
        
        $lang = app('lang', 'en');

        if(isset($_SESSION['transalation']->$translation)) {
            if(isset($_SESSION['transalation']->$word->$lang)) {
                $translation = $_SESSION['transalation']->$word->$lang;
            }
        }

        return $translation;
    }

    public static function define($word, Array $values) {
        foreach ($values as $lang => $translation) {
            $_SESSION['transalation']->$word->$lang = $translation;
        }
    }
}
?>