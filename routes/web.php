<?php

use App\Mail\ReservationConfirmationMail;
use App\Models\Book;
use App\Models\Reservation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $model = Reservation::all()->first();
    Mail::to($model->user->email)->send(new ReservationConfirmationMail($model));
    return view('welcome');
});

Route::get("/test",function (){
    if(!Redis::exists("books")){
        $books = Book::all();
        $test = Redis::set('books',$books);
        return "hello";
    }
    $test = Redis::get("books");
    return $test;
});
Route::get("/test2",function (){
    $test = Cache::remember("bvb",null,function(){
        return Book::all();
    });
    return $test;
});
