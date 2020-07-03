<?php

namespace Services;

class Presenter {
    private static $data = [];

    public static function present(string $presenter, array $data = []) {
        self::$data = array_merge(self::$data, $data);
        self::$data;
        $data = self::$data;

        $presenter = str_replace('.', DIRECTORY_SEPARATOR, $presenter); 

        if(file_exists($_SERVER['DOCUMENT_ROOT'].'/presenters/'.$presenter.'.php')) {
            require_once( $_SERVER['DOCUMENT_ROOT'].'/presenters/'.$presenter.'.php' );
        } else {
            echo "
            <div class='ui icon message orange large'>
                <i onclick='window.history.back()' class='close icon'></i>
                <i class='thumbs down outline icon'></i>
                <div class='content'>
                    <div class='header'>
                    ". Translation::translate('error') ." 404 - ". Translation::translate('page_not_found') ."
                    </div>
                    <div class='divider'></div>
                    <p>". $_SERVER['DOCUMENT_ROOT'].'/presenters/'.$presenter.'.php' ."</p>
                </div>
            </div>";
            exit();
        }
        
    }
}

?>