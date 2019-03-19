<?php
/*
*   Make Routes for Login and register 
*   Make Routes for the URLS
*/
Route::post('v1/login', 'ApiController@login');
Route::post('v1/register', 'ApiController@register');
 
/*Route::group(['middleware' => 'auth.jwt'], function () 
{
    Route::get('v1/logout', 'ApiController@logout');
    Route::get('v1/user', 'ApiController@getAuthUser');
    Route::get('v1/urls', 'URLController@index');
    Route::get('v1/urls/{id}', 'URLController@show');
    Route::post('v1/urls', 'URLController@store');
    Route::put('v1/urls/{id}', 'URLController@update');
    Route::delete('v1/urls/{id}', 'URLController@destroy');
});*/
