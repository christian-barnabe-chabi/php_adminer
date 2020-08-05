<?php

namespace App\Providers;

use Abstracts\Resource;
use Services\Auth;
use Services\Presenter;
use Services\Router;
use Services\Translation;

class Login extends Resource {

    public function __construct()
    {
        if(!app('mustAuth') || Auth::user()) {
            Router::redirect(app('entrypoint', 'dashboard'));
        }
    }


    public function handle(array $data = [])
    {
        $data = (Object) $data;
    
        if (isset($data->email) and isset($data->password)) {
            Auth::attempt($data->email, $data->password);
    
            $data->error = Translation::translate('login_error');
        }
        if( ! Auth::user() ) {
            if(file_exists($_SERVER['DOCUMENT_ROOT'].'/views/login.php')) {
                Presenter::present('login', (Array)$data);
            } else {
                Presenter::present('generics.login', (Array)$data);
            }
            exit();
        }
    }
}

?>
