<div>
    <p>Dear {{ $reservation->user->name }},</p>

    <p>We are pleased to inform you that your reserved book, "{{ $reservation->book->title }}", is now available for
    pickup.</p>

    <p>📅 Pickup Time: {{ \Carbon\Carbon::parse($reservation->rental_date)->format('F j, Y')}} at {{ \Carbon\Carbon::parse($reservation->rental_time)->format("H:i") }}</p>

    <p>Please visit the library before the deadline to collect your book. If you have any questions or need an extension,
    feel free to contact us.</p>

    <p>Happy reading! 📖</p>
</div>