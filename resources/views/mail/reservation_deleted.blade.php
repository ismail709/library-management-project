<div>
    <p>Dear {{ $reservation->user->name }},</p>

    <p>Your reservation for the book "{{ $reservation->book->title }}" has been deleted because it was overdue.</p>

    <p>If you need further assistance, please contact us.</p>
    
    <p>Thank you.</p>
</div>