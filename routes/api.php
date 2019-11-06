<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function() {
    Route::middleware(['auth:api'])->group(function() {
        // User Routes
        Route::get('/user', 'UserController@show')->name('user.show'); 
        Route::post('/logout', 'UserController@logout')->name('user.logout');

        // Team Routes
        Route::get('/teams}', 'TeamController@index')->name('teams.index');
        Route::get('/teams/{team}', 'TeamController@show')->name('teams.show');
    });

});
