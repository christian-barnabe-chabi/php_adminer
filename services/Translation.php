<?php
namespace Services;

class Translation {

    public static function translate(string $word) {
        if(!isset($_SESSION['translation'])) {

            $file = fopen($_SERVER['DOCUMENT_ROOT']."/.translations.json", "r");
            $file_lines = '';
            while($line = fgets($file)) {
                $file_lines .=  $line;
            }

            $file_lines = str_replace("\n", '', $file_lines);

            $_SESSION['translation'] = json_decode($file_lines);

        }
        $translation = trim($word);
        
        $lang = app('lang', 'en');

        if(isset($_SESSION['translation']->$translation)) {
            if(isset($_SESSION['translation']->$word->$lang)) {
                $translation = $_SESSION['translation']->$word->$lang;
            }
        }

        return $translation;
    }

    public static function define($word, Array $values) {
        if(isset($_SESSION['translation'])) {

            if(empty($_SESSION['translation'])) {
                $_SESSION['translation'] = (object)[];
            }

            foreach ($values as $lang => $translation) {
                $_SESSION['translation']->$word->$lang = $translation;
            }
        }
    }

    public static function get($word) {

        if(isset($_SESSION['translation']->$word)) {
            return $_SESSION['translation']->$word;
        }
        
        return null;
    }
}
?>