<?php

namespace Services;

class Presenter {
    private static $data = [];

    public static function present(string $presenter, array $data = []) {
        self::$data = array_merge(self::$data, $data);
        self::$data;
        $data = self::$data;

        $presenter = str_replace('.', DIRECTORY_SEPARATOR, $presenter); 

        if(file_exists($_SERVER['DOCUMENT_ROOT'].'/views/'.$presenter.'.php')) {
            require_once( $_SERVER['DOCUMENT_ROOT'].'/views/'.$presenter.'.php' );
        } else {

            Presenter::present('generics.error', [
                'error_code' => 404,
                'error_info' => Translation::translate('page_not_found'),
                'error_description' => "The file <code>".$_SERVER['DOCUMENT_ROOT'].'/views/'.$presenter.".php</code> not found"
            ]);
            exit();

            echo "
            <div class='ui icon message orange large'>
                <i onclick='window.history.back()' class='close icon'></i>
                <i class='thumbs down outline icon'></i>
                <div class='content'>
                    <div class='header'>
                    ". Translation::translate('error') ." 404 - ". Translation::translate('page_not_found') ."
                    </div>
                    <div class='divider'></div>
                    <p>". $_SERVER['DOCUMENT_ROOT'].'/views/'.$presenter.'.php' ."</p>
                </div>
            </div>";
            // exit();
        }
        
    }
}

?>