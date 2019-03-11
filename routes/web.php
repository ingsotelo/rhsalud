<?php


Route::get('/refreshcaptcha', function () {
    return response()->json(['captcha'=> captcha_img()]);
});

Route::get('/', function () {
    return view('welcome');
});


Route::get('contact-us', 'ContactUSController@contactUS')->name('contactus');
Route::post('contact-us', 'ContactUSController@contactUSPost')->name('contactus.store');

Auth::routes(['verify' => true]);

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/dashboard', 'HomeController@dashboard')->name('dashboard');
Route::get('/uploaddata','UsersController@uploadData')->name('uploaddata');

Route::resource('users', 'UsersController');

Route::get('/downloadxml/{cfdi}','CfdiController@downloadXml')->name('downloadxml');
Route::get('/downloadpdf/{cfdi}','CfdiController@downloadPdf')->name('downloadpdf');
Route::post('/uploadcfdis','CfdiController@uploadCfdis')->name('uploadcfdis');
Route::get('/uploadcfdis','CfdiController@index')->name('cfdis.index');



