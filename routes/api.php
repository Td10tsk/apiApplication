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

/**
 * route groups of api with request methods
 */

Route::group(['prefix' => 'v1/document'], function(){
    Route::post('/', 'Api\Document\v1\DocumentApiController@createDocument');
    Route::get('/{id}', 'Api\Document\v1\DocumentApiController@getDocument');
    Route::patch('/{id}', 'Api\Document\v1\DocumentApiController@editDocument');
    Route::post('/{id}/publish', 'Api\Document\v1\DocumentApiController@publishDocument');
    Route::get('/', 'Api\Document\v1\DocumentApiController@showDocument');
});
Route::post('v1/login', 'Api\Document\v1\DocumentApiController@auth');
