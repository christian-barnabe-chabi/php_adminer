<?php

use App\Scaffolding\ResourceScaffold;
use Services\Auth;
use Services\Resource;

class MenuScaffold {
    public function __construct() {

        /**
         * @param str resource_name
         * @param str match
         * @param str icon (semantic ui icon class)
         */
		ResourceScaffold::define('Dashboard', 'dashboard', 'tachometer alternate');
		ResourceScaffold::render();
    }
}

?>
