<?php

Route::resource('post/ratable', 'PostRatableController')->only(['store']);
Route::resource('post/api', 'PostIpController')->only(['index']);
Route::resource('post', 'PostController')->only(['index', 'store']);
Route::resource('generator', 'GeneratorController')->only(['index']);