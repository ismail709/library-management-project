<?php

use App\Mail\ReservationConfirmationMail;
use App\Models\Reservation;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $model = Reservation::all()->first();
    Mail::to($model->user->email)->send(new ReservationConfirmationMail($model));
    return view('welcome');
});

Route::get("/test",function (){
    return "Done";
});
