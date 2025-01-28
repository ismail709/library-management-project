<?php

namespace App\Filament\Resources\ReservationResource\Pages;

use App\Filament\Resources\ReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateReservation extends CreateRecord
{
    protected static string $resource = ReservationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure rental_date is parsed correctly as a date
        $rentalDate = \Carbon\Carbon::parse($data['rental_date']);
        
        // Add 7 days to the rental date
        $data['due_date'] = $rentalDate->addDays(7)->toDateString();
    
        return $data;
    }
}
