<?php

/*
|--------------------------------------------------------------------------
| Api Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Mods the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

/*
|--------------------------------------------------------------------------
| Common Routes
|--------------------------------------------------------------------------
|
*/

Route::get('/backend/types', [ 'as' => 'backend.types', 'uses' => 'SettingsController@backendType']);

Route::get('/frontend/types', [ 'as' => 'frontend.types', 'uses' => 'SettingsController@frontendType']);

Route::get('/select/sources', [ 'as' => 'select.sources', 'uses' => 'SettingsController@selectSources']);

/*
|--------------------------------------------------------------------------
| Entity Routes
|--------------------------------------------------------------------------
|
*/

Route::get('/entities', [ 'as' => 'entity.list', 'uses' => 'EntityController@list']);

Route::get('/entities/{code}', [ 'as' => 'entity.get', 'uses' => 'EntityController@get']);

/*
|--------------------------------------------------------------------------
| Attribute Set Routes
|--------------------------------------------------------------------------
|
*/

Route::get('/entities/{code}/set', [ 'as' => 'set.list', 'uses' => 'AttributeSetController@list']);

Route::get('/entities/{code}/set/{id}', [ 'as' => 'set.get', 'uses' => 'AttributeSetController@get']);

Route::post('/entities/{code}/set', [ 'as' => 'set.create', 'uses' => 'AttributeSetController@create']);

Route::put('/entities/{code}/set/{id}', [ 'as' => 'set.update', 'uses' => 'AttributeSetController@update']);

Route::put('/entities/{code}/set/{id}/regroup', [ 'as' => 'set.update_group', 'uses' => 'AttributeSetController@reGroup']);

Route::delete('/entities/{code}/set/{id}', [ 'as' => 'set.delete', 'uses' => 'AttributeSetController@remove']);

/*
|--------------------------------------------------------------------------
| Attribute Group Routes
|--------------------------------------------------------------------------
|
*/

Route::get('/entities/{code}/set/{setId}/group', [ 'as' => 'group.list', 'uses' => 'AttributeGroupController@list']);

Route::get('/entities/{code}/set/{setId}/group/{id}/attributes', [ 'as' => 'group.attribute.list', 'uses' => 'AttributeGroupController@listAttributes']);

Route::get('/entities/{code}/set/{setId}/group/{id}', [ 'as' => 'group.get', 'uses' => 'AttributeGroupController@get']);

Route::post('/entities/{code}/set/{setId}/group', [ 'as' => 'group.create', 'uses' => 'AttributeGroupController@create']);

Route::put('/entities/{code}/set/{setId}/group/{id}', [ 'as' => 'group.update', 'uses' => 'AttributeGroupController@update']);

Route::delete('/entities/{code}/set/{setId}/group/{id}', [ 'as' => 'group.delete', 'uses' => 'AttributeGroupController@remove']);

/*
|--------------------------------------------------------------------------
| Attribute Routes
|--------------------------------------------------------------------------
|
*/

Route::get('/entities/{code}/attributes', [ 'as' => 'attribute.list', 'uses' => 'AttributeController@list']);

Route::get('/entities/{code}/attributes/{id}', [ 'as' => 'attribute.get', 'uses' => 'AttributeController@get']);

Route::post('/entities/{code}/attributes', [ 'as' => 'attribute.create', 'uses' => 'AttributeController@create']);

Route::put('/entities/{code}/attributes/{id}', [ 'as' => 'attribute.update', 'uses' => 'AttributeController@update']);

Route::delete('/entities/{code}/attributes/{id}', [ 'as' => 'attribute.delete', 'uses' => 'AttributeController@remove']);

Route::post('/entities/{entityCode}/attributes/map', [ 'as' => 'attribute.map', 'uses' => 'AttributeController@map']);

/*
|--------------------------------------------------------------------------
| Attribute Options Routes
|--------------------------------------------------------------------------
|
*/

Route::get('/entities/{entityCode}/attributes/{attrCode}/options', [ 'as' => 'options.list', 'uses' => 'AttributeOptionsController@list']);

Route::post('/entities/{entityCode}/attributes/{attrCode}/options', [ 'as' => 'options.create', 'uses' => 'AttributeOptionsController@create']);

Route::put('/entities/{entityCode}/attributes/{attrCode}/options', [ 'as' => 'options.update', 'uses' => 'AttributeOptionsController@update']);

Route::delete('/entities/{entityCode}/attributes/{attrCode}/options', [ 'as' => 'options.delete', 'uses' => 'AttributeOptionsController@remove']);

