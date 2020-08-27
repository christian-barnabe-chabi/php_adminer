<?php

use Services\Scaffolders\ResourceScaffold;
use Services\Auth;
use Services\Resource;

class MenuScaffold {
    public function __construct() {

        /**
         * @param str resource_name
         * @param str route
         * @param str icon (semantic ui icon class)
         */
		ResourceScaffold::define('Dashboard', 'dashboard', 'tachometer alternate');
		ResourceScaffold::render();
    }
}

?>
