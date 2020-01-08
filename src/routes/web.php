<?php

Route::group([
    'middleware' => ['web', 'checkUserAuth'],
], function () {
    Route::get('index', 'itobuz\laravelgooglesheetintegration\GoogleSheetController@index')->name('googlesheet-signin');
    Route::get('authenticateUser', 'itobuz\laravelgooglesheetintegration\GoogleSheetController@authenticateUser')->name('authenticateUser');
    Route::get('userAuthenticated', 'itobuz\laravelgooglesheetintegration\GoogleSheetController@userAuthenticated')->name('userAuthenticated');
    Route::get('googleSignOut', 'itobuz\laravelgooglesheetintegration\GoogleSheetController@googleSignOut')->name('googleSignOut');

    // Route::group([
    //     'middleware' => ['googleAuth'],
    // ], function () {
        Route::get('getFileOrFolder', 'itobuz\laravelgooglesheetintegration\GoogleSheetController@getFileOrFolder')->name('getFileOrFolder');
        Route::post('storeFolder', 'itobuz\laravelgooglesheetintegration\GoogleSheetController@storeFolderId')->name('storeFolder');
        Route::get('setAction', 'itobuz\laravelgooglesheetintegration\GoogleSheetController@setAction')->name('setAction');
        Route::get('updatesettings', 'itobuz\laravelgooglesheetintegration\GoogleSheetController@updatesettings')->name('updatesettings');
        Route::get('getTokenDetails', 'itobuz\laravelgooglesheetintegration\GoogleSheetController@getTokenDetails')->name('getTokenDetails');
    // });
});
