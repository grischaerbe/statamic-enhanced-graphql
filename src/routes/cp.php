<?php

use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
  Route::get('/legrisch/statamic-enhanced-graphql', 'SettingsController@index')->name('legrisch.statamic-enhanced-graphql.index');
  Route::post('/legrisch/statamic-enhanced-graphql', 'SettingsController@update')->name('legrisch.statamic-enhanced-graphql.update');
});
