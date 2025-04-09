<?php

use App\Http\Controllers\CatListController;
use App\Http\Controllers\CreateCatController;
use App\Http\Controllers\DeleteCatController;
use App\Http\Controllers\UpdateCatController;
use Illuminate\Support\Facades\Route;

Route::get('/cats', CatListController::class)->name('api.cat.list');
Route::post('/cats', CreateCatController::class)->name('api.cat.create');
Route::put('/cats/{id}', UpdateCatController::class)->whereNumber('id')->name('api.cat.update');
Route::delete('/cats/{id}', DeleteCatController::class)->whereNumber('id')->name('api.cat.delete');