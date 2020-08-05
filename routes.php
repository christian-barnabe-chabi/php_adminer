<?php

use Services\Route;

Route::intercept('/dashboard(\w)*/', "App\Providers\Dashboard@handle");
Route::intercept('/logout(\w)*/', "App\Providers\Logout@handle");
Route::intercept('/login(\w)*/', "App\Providers\Login@handle");

?>
