<div>
    <p>Dear {{ $reservation->user->name }},</p>

    <p>We noticed that your borrowed book, "{{ $reservation->book->title }}", was due on
    {{ \Carbon\Carbon::parse($reservation->due_date)->format('F j, Y') }}, but it has not been returned yet.</p>

    <p>To avoid further penalties or restrictions, please return the book as soon as possible.</p>

    <p>If you have already returned the book, please disregard this message. For any questions or concerns, feel free to
    contact us.</p>

    <p>Thank you for your prompt attention.</p>

    <p>Best regards,</p>
</div>