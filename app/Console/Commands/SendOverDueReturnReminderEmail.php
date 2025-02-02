<?php

namespace App\Console\Commands;

use App\Mail\OverdueReminderMail;
use App\Models\Reservation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendOverDueReturnReminderEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:overdue-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command send an email to all users who did not return their rented book on time.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $reservations = Reservation::where('due_date','<',now())
                        ->where("status",'rented')
                        ->get();
                        
        foreach ($reservations as $reservation) {
            Mail::to($reservation->user->email)->send(new OverdueReminderMail($reservation));

            $this->info("Overdue Reminder sent to {$reservation->user->email} for book '{$reservation->book->title}' (Reservation ID: {$reservation->id}).");
        }

        $this->info('Reservations have been processed successfully.');
    }
}
