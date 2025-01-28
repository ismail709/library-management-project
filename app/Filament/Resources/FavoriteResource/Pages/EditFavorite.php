<?php

namespace App\Filament\Resources\FavoriteResource\Pages;

use App\Filament\Resources\FavoriteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFavorite extends EditRecord
{
    protected static string $resource = FavoriteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
