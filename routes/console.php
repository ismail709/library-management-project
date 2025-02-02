<?php

use App\Console\Commands\SendDeletedReservationsReminderEmail;
use App\Console\Commands\SendOverDueReturnReminderEmail;
use App\Console\Commands\SendReturnReminderEmail;
use Illuminate\Support\Facades\Schedule;


/*  
    this command delete users' reservations who didn't pickup
    their book on time and send them email notification.
*/
Schedule::command(SendDeletedReservationsReminderEmail::class)->daily();
/*  
    this command sends email notifications to the users whose
    due date is the following day.
*/
Schedule::command(SendReturnReminderEmail::class)->daily();
/*  
    this command sends email notifications to the user who
    did not return their rented book on time
*/
Schedule::command(SendOverDueReturnReminderEmail::class)->weekly();