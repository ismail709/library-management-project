<?php

namespace App\Models;

use App\Jobs\ClearReservationsCacheJob;
use App\Mail\ReservationConfirmationMail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class Reservation extends Model
{
    /** @use HasFactory<\Database\Factories\ReservationFactory> */
    use HasFactory,SoftDeletes;

    protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function book(){
        return $this->belongsTo(Book::class);
    }

    protected static function booted(){
        static::created(function ($model) {

            ClearReservationsCacheJob::dispatch($model->user_id);

            Mail::to($model->user->email)->send(new ReservationConfirmationMail($model));
            $model->book->decrement("stock");
        });
        static::updated(function ($model) {

            ClearReservationsCacheJob::dispatch($model->user_id);

            if(in_array($model->status, ["returned","late"])){
                $model->book->increment("stock");
            }
        });
        static::deleted(function ($model) {

            ClearReservationsCacheJob::dispatch($model->user_id);

            if(in_array($model->status, ["pending","rented"])){
                $model->book->increment("stock");
            }
        });
    }
}
