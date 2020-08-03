<?php

namespace App\Resources;

use Abstracts\Resource;
use Services\Auth;
use Services\Presenter;
use Services\Request;

class Dashboard extends Resource {


    public function handle(array $data = [])
    {
        Presenter::present('dashboard');
    }
}

?>