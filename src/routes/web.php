<?php

Route::group([
    'middleware' => ['web', 'checkUserAuth'],
], function () {
    Route::get('index', 'laravelstudio\laravelgooglesheetintegration\GoogleSheetController@index')->name('googlesheet-signin');
    Route::get('authenticateUser', 'laravelstudio\laravelgooglesheetintegration\GoogleSheetController@authenticateUser')->name('authenticateUser');
    Route::get('userAuthenticated', 'laravelstudio\laravelgooglesheetintegration\GoogleSheetController@userAuthenticated')->name('userAuthenticated');
    Route::get('googleSignOut', 'laravelstudio\laravelgooglesheetintegration\GoogleSheetController@googleSignOut')->name('googleSignOut');

    // Route::group([
    //     'middleware' => ['googleAuth'],
    // ], function () {
        Route::get('getFileOrFolder', 'laravelstudio\laravelgooglesheetintegration\GoogleSheetController@getFileOrFolder')->name('getFileOrFolder');
        Route::post('storeFolder', 'laravelstudio\laravelgooglesheetintegration\GoogleSheetController@storeFolderId')->name('storeFolder');
        Route::get('setAction', 'laravelstudio\laravelgooglesheetintegration\GoogleSheetController@setAction')->name('setAction');
        Route::get('updatesettings', 'laravelstudio\laravelgooglesheetintegration\GoogleSheetController@updatesettings')->name('updatesettings');
        Route::get('getTokenDetails', 'laravelstudio\laravelgooglesheetintegration\GoogleSheetController@getTokenDetails')->name('getTokenDetails');
    // });
});
