<?php

namespace App\Console\Commands;

use App\Mail\ReservationDeletedNotificationMail;
use App\Models\Reservation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDeletedReservationsReminderEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:delete-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command send an email to all users whose reservations was deleted because they did not pickup their book on time.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $reservations = Reservation::where('rental_date', '<', now())
                                ->where('status',"pending")
                                ->get();

        foreach ($reservations as $reservation) {
            // Send notification email
            Mail::to($reservation->user->email)->send(new ReservationDeletedNotificationMail($reservation));

            // Delete the reservation
            $reservation->delete();

            // Output log in the console
            $this->info("Reservation ID {$reservation->id} deleted and email sent to {$reservation->user->email}.");
        }

        $this->info('Reservations have been processed successfully.');
    }
}
