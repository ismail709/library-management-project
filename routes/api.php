<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('/users')->group(function () {
    Route::post('/',[UserController::class,'store']);
    Route::put("/edit/{user}",[UserController::class,"update"])->middleware("auth:sanctum");
    Route::post('/login',[UserController::class,'login']);
    Route::get('/logout',[UserController::class,'logout'])->middleware("auth:sanctum");
    Route::get('/reservations',[UserController::class,'reservations'])->middleware("auth:sanctum");
    Route::get('/favorites',[UserController::class,'favorites'])->middleware("auth:sanctum");
});

Route::prefix("/books")->group(function () {
    Route::get("/",[BookController::class,"index"]);
    Route::get('/search', [BookController::class, 'search']);
    Route::get("/popular",[BookController::class,"popular"]);
    Route::get("/mostrented",[BookController::class,"mostRented"]);
    Route::get("/recent",[BookController::class,"recent"]);
    Route::get("/{book:slug}",[BookController::class,"find"]);
    Route::get("/c/{category}",[BookController::class,"byCategory"]);
    Route::get("/cl/{collection}",[BookController::class,"byCollection"]);
});

Route::prefix("/categories")->group(function () {
    Route::get("/",[CategoryController::class,"index"]);
    Route::get("/featured",[CategoryController::class,"featured"]);
    Route::get("/{category}",[CategoryController::class,"find"]);
});

Route::prefix("/reservations")->group(function () {
    Route::post("/",[ReservationController::class,"store"])->middleware('auth:sanctum');
    Route::put("/{reservation}",[ReservationController::class,"update"]);
    Route::delete("/{reservation}",[ReservationController::class,"destroy"]);
})->middleware('auth:sanctum');

Route::prefix("/favorites")->group(function () {
    Route::get("/check/{book}",[FavoriteController::class,"isFavorite"])->middleware("auth:sanctum");
    Route::get("/{book}",[FavoriteController::class,"toggleFavorite"])->middleware("auth:sanctum");
});