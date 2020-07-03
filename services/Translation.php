<?php
namespace Services;

class Translation {
    static $translations;

    public static function translate(string $word) {
        $translation = trim($word);
        if(empty(app('lang'))) {
            exit('Language value not set in env variables');
        }
        $lang = app('lang');

        if(self::$translations == null) {
            $file = fopen($_SERVER['DOCUMENT_ROOT']."/.translations.json", "r");
            $file_lines = '';
            while($line = fgets($file)) {
                $file_lines .=  $line;
            }

            $file_lines = str_replace("\n", '', $file_lines);

            self::$translations = json_decode($file_lines);
        }

        if(isset(self::$translations->$translation)) {
            if(isset(self::$translations->$word->$lang)) {
                $translation = self::$translations->$word->$lang;
            }
        }

        return $translation;
    }

    public static function define($word, Array $values) {
        foreach ($values as $lang => $translation) {
            self::$translations->$word->$lang = $translation;
        }
    }
}
?>