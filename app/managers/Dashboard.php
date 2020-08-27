<?php

namespace App\Managers;

use Abstracts\Resource;
use Services\Auth;
use Services\Presenter;
use Services\Request;

class Dashboard extends Resource {


    public function handle(array $data = [])
    {
        if(file_exists($_SERVER['DOCUMENT_ROOT'].'/views/dashboard.php')) {
            Presenter::present('dashboard', (Array)$data);
        } else {
            Presenter::present('generics.dashboard', (Array)$data);
        }
    }
}

?>
