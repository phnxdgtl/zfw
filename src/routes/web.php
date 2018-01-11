<?php

/*
** Note that without the "web" middleware we can't access $errors
*/

Route::get('zfw/{thanks?}', 'Sevenpointsix\Zfw\ZfwController@test')
        ->name('zfw-test')
        ->where('thanks', 'thanks')
        ->middleware('web');

Route::post('zfw/{form}', 'Sevenpointsix\Zfw\ZfwController@formHandler')->name('zfw')->middleware('web');