<?php
/*
*   Make Routes for Login and register 
*   Make Routes for the URLS
*/
Route::post('v1/login', 'ApiController@login');
Route::post('v1/register', 'ApiController@register');
 
Route::group(['middleware' => 'auth.jwt'], function () 
{
    Route::get('v1/logout', 'ApiController@logout');
    Route::get('v1/user', 'ApiController@getAuthUser');
    Route::get('v1/urls', 'UrlController@index');
    Route::get('v1/urls/{id}', 'UrlController@show');
    Route::post('v1/urls', 'UrlController@store');
    Route::put('v1/urls/{id}', 'UrlController@update');
    Route::delete('v1/urls/{id}', 'UrlController@destroy');
    Route::resource('v1/urls','UrlController');
});
