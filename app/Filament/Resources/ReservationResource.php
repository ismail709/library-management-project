<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReservationResource\Pages;
use App\Models\Reservation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user',"name")
                    ->label('User')
                    ->required()
                    ->preload(),
                Forms\Components\Select::make('book_id')
                    ->relationship('book',"title")
                    ->label('Book')
                    ->required()
                    ->preload(),
                Forms\Components\DatePicker::make('rental_date')
                    ->required(),
                Forms\Components\TimePicker::make('rental_time')
                    ->format('h:i')
                    ->displayFormat('h:i')
                    ->hoursStep(1)
                    ->minutesStep(15)
                    ->required(),
                Forms\Components\DatePicker::make('due_date'),
                Forms\Components\DatePicker::make('return_date'),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'rented' => 'Rented',
                        'returned' => 'Returned',
                        'late' => 'Late',
                    ])
                    ->default('pending')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('book.title')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('rental_date')
                    ->since()
                    ->dateTimeTooltip('d F Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('rental_time')
                    ->time('h:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->date('d F Y'),
                Tables\Columns\TextColumn::make('return_date')
                    ->date('d F Y')
                    ->placeholder('Not returned yet!'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'rented' => 'warning',
                        'returned' => 'success',
                        'late' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'rented' => 'Rented',
                        'returned' => 'Returned',
                        'late' => 'Late',
                    ])
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('return')
                    ->label('Mark as Returned') // Optional: Set a custom label
                    ->action(function ($record) {
                        if ($record->return_date === null) {
                            $returnDate = now();
                            $status = $returnDate->lessThanOrEqualTo($record->due_date) ? 'returned' : 'late';
                            
                            $record->update([
                                'return_date' => $returnDate,
                                'status' => $status,
                            ]);
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('return')
                        ->label('Mark Selected as Returned') // Optional: Custom label for bulk action
                        ->action(fn ($records) => $records
                                        ->filter(fn ($record) => $record->return_date === null)
                                        ->each(function ($record) {
                                            $returnDate = now();
                                            $status = $returnDate->lessThanOrEqualTo($record->due_date) ? 'returned' : 'late';
                        
                                            $record->update([
                                                'return_date' => $returnDate,
                                                'status' => $status,
                                            ]);
                                        })
                        )->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReservations::route('/'),
            'create' => Pages\CreateReservation::route('/create'),
            'view' => Pages\ViewReservation::route('/{record}'),
            'edit' => Pages\EditReservation::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
