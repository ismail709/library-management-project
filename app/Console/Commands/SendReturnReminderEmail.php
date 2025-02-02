<?php

namespace App\Console\Commands;

use App\Mail\ReturnReminderMail;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendReturnReminderEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:return-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command sends an email to all the users whose due date is tomorrow.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tomorrow = Carbon::tomorrow()->toDateString();

        $reservations = Reservation::where('due_date', $tomorrow)
                    ->where('status',"rented")
                    ->get();
                    
        if($reservations->isEmpty()){
            $this->info('No reservations are due for return tomorrow.');
            return;
        }

        foreach ($reservations as $reservation) {
            Mail::to($reservation->user->email)->send(new ReturnReminderMail($reservation));

            $this->info("Reminder sent to {$reservation->user->email} for book '{$reservation->book->title}' (Reservation ID: {$reservation->id}).");
        }

        $this->info('Return reminder emails have been sent successfully.');

    }
}
