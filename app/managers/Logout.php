<?php

namespace App\Managers;

use Abstracts\Resource as AbstractsResource;
use Services\Router;

class Logout extends AbstractsResource {

    public function __construct()
    {
        unset($_SESSION['oauth']);
        unset($_SESSION['ENV']);
        unset($_SESSION['translation']);
        Router::redirect("login");
    }

    public function handle(array $data = [])
    {
        
    }
}

?>