<div>
    <p>Dear {{ $reservation->user->name }},</p>
    
    <p>This is a friendly reminder that your borrowed book, "{{ $reservation->book->title }}", is due for return tomorrow
    ({{ \Carbon\Carbon::parse($reservation->due_date)->format('F j, Y') }}).</p>

    <p>Please make sure to return it on time to avoid any late fees and to allow others to enjoy the book as well.</p>

    <p>If you have any questions or need an extension, feel free to contact us.</p>

    <p>Thank you</p>
</div>