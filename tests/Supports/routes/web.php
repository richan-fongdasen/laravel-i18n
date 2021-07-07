<?php

Route::group(['prefix' => \I18n::routePrefix()], function () {
    Route::get('foo', FooController::class)->name('foo');
    Route::get('bar', BarController::class)->name('bar');
});
