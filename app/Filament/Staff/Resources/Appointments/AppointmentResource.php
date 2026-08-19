<?php

namespace App\Filament\Staff\Resources\Appointments;

use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Appointment;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\BelongsToSelect;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Staff\Resources\AppointmentResource\Pages;
use App\Filament\Staff\Resources\AppointmentResource\RelationManagers;
use App\Filament\Staff\Resources\Appointments\Pages\EditAppointment;
use App\Filament\Staff\Resources\Appointments\Pages\ListAppointments;
use App\Filament\Staff\Resources\Appointments\Pages\CreateAppointment;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'موعد' : 'Appointment';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'مواعيد' : 'Appointments';
    }

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('appointment_type_id')
                    ->relationship('appointmentType', 'name')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('property_id')
                    ->relationship('property', 'title')
                    ->required(),
                DatePicker::make('appointment_date')
                    ->label(__('filament.fields.appointment_date'))
                    ->required(),
                Select::make('status')
                    ->label(__('filament.fields.status'))
                    ->options(fn () => app()->getLocale() === 'ar' ? [
                        'requested' => 'مطلوب',
                        'confirmed' => 'مؤكد',
                        'canceled' => 'ملغى',
                    ] : [
                        'requested' => 'Requested',
                        'confirmed' => 'Confirmed',
                        'canceled' => 'Canceled',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('appointment_date')
                    ->label(__('filament.fields.date'))
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label(__('filament.fields.user'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('property.title')
                    ->label(__('filament.fields.property'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('agent.name')
                    ->label(__('filament.fields.agent'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->label(__('filament.fields.status'))
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => ListAppointments::route('/'),
            'create' => CreateAppointment::route('/create'),
            'edit' => EditAppointment::route('/{record}/edit'),
        ];
    }
}
