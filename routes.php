<?php

use Services\Route;

Route::intercept('/dashboard/', "App\Managers\Dashboard@handle");
Route::intercept('/logout/', "App\Managers\Logout@handle");
Route::intercept('/login*/', "App\Managers\Login@handle");

?>
