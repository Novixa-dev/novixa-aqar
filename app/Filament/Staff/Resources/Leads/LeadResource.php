<?php

namespace App\Filament\Staff\Resources\Leads;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use App\Filament\Staff\Resources\Leads\Pages\ListLeads;
use App\Filament\Staff\Resources\Leads\Pages\CreateLead;
use App\Filament\Staff\Resources\Leads\Pages\EditLead;
use App\Filament\Staff\Resources\LeadResource\Pages;
use App\Models\Lead;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'عميل محتمل' : 'Lead';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'عملاء محتملون' : 'Leads';
    }

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-group';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required(),
                TextInput::make('email')->email()->required(),
                TextInput::make('phone'),
                Select::make('interest')
                    ->options(fn () => app()->getLocale() === 'ar' ? [
                        'buying' => 'شراء',
                        'selling' => 'بيع',
                        'renting' => 'إيجار',
                        'other' => 'أخرى',
                    ] : [
                        'buying' => 'Buying',
                        'selling' => 'Selling',
                        'renting' => 'Renting',
                        'other' => 'Other',
                    ]),
                Textarea::make('message'),
                Select::make('status')
                    ->options(fn () => app()->getLocale() === 'ar' ? [
                        'new' => 'جديد',
                        'contacted' => 'تم التواصل',
                        'qualified' => 'مؤهل',
                        'lost' => 'ملغي',
                        'converted' => 'تم التحويل',
                    ] : [
                        'new' => 'New',
                        'contacted' => 'Contacted',
                        'qualified' => 'Qualified',
                        'lost' => 'Lost',
                        'converted' => 'Converted',
                    ])
                    ->required(),
                TextInput::make('score')
                    ->disabled()
                    ->helperText(__('Score is automatically calculated')),
                Select::make('category')
                    ->options(fn () => app()->getLocale() === 'ar' ? [
                        'hot' => 'مرتفع الاهتمام',
                        'warm' => 'متوسط الاهتمام',
                        'cold' => 'منخفض الاهتمام',
                    ] : [
                        'hot' => 'Hot',
                        'warm' => 'Warm',
                        'cold' => 'Cold',
                    ])
                    ->required(),
                DateTimePicker::make('last_contacted_at'),
                Repeater::make('activities')
                    ->relationship('activities')
                    ->schema([
                        TextInput::make('type'),
                        Textarea::make('description'),
                        DateTimePicker::make('created_at'),
                    ])
                    ->columnSpan(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('email')->sortable()->searchable(),
                TextColumn::make('interest'),
                TextColumn::make('status')
                    ->formatStateUsing(\App\Support\StatusLabels::for('lead'))
                    ->color(\App\Support\StatusLabels::color('lead')),
                TextColumn::make('score')->sortable(),
                TextColumn::make('created_at')
                    ->dateTime('d-M-Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(fn () => app()->getLocale() === 'ar' ? [
                        'new' => 'جديد',
                        'contacted' => 'تم التواصل',
                        'qualified' => 'مؤهل',
                        'lost' => 'ملغي',
                        'converted' => 'تم التحويل',
                    ] : [
                        'new' => 'New',
                        'contacted' => 'Contacted',
                        'qualified' => 'Qualified',
                        'lost' => 'Lost',
                        'converted' => 'Converted',
                    ]),
                SelectFilter::make('interest')
                    ->options(fn () => app()->getLocale() === 'ar' ? [
                        'buying' => 'شراء',
                        'selling' => 'بيع',
                        'renting' => 'إيجار',
                        'other' => 'أخرى',
                    ] : [
                        'buying' => 'Buying',
                        'selling' => 'Selling',
                        'renting' => 'Renting',
                        'other' => 'Other',
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
            'index' => ListLeads::route('/'),
            'create' => CreateLead::route('/create'),
            'edit' => EditLead::route('/{record}/edit'),
        ];
    }
}
