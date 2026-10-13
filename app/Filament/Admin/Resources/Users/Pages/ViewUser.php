<?php

namespace App\Filament\Admin\Resources\Users\Pages;

use App\Filament\Admin\Resources\Users\UserResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('User Profile')
                    ->columns(2)
                    ->schema([
                        ImageEntry::make('profile_photo_url')
                            ->label(__('filament.fields.profile_photo'))
                            ->circular()
                            ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=7F9CF5&background=EBF4FF')
                            ->columnSpanFull(),
                        
                        TextEntry::make('name')
                            ->label(__('filament.fields.full_name'))
                            ->size('lg')
                            ->weight('bold'),
                        
                        TextEntry::make('email')
                            ->label(__('filament.fields.email_address'))
                            ->copyable()
                            ->icon('heroicon-o-envelope'),
                        
                        IconEntry::make('email_verified_at')
                            ->label(__('filament.fields.email_verified'))
                            ->boolean()
                            ->trueIcon('heroicon-o-check-badge')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger'),
                        
                        TextEntry::make('email_verified_at')
                            ->label(__('filament.fields.verified_at'))
                            ->dateTime('M d, Y H:i')
                            ->placeholder(__('Not verified')),
                    ]),
                
                Section::make('Roles & Permissions')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('roles.name')
                            ->label(__('filament.fields.assigned_roles'))
                            ->badge()
                            ->color('success')
                            ->formatStateUsing(\App\Support\StatusLabels::for('role'))
                            ->placeholder(__('No roles assigned'))
                            ->columnSpanFull(),
                        
                        TextEntry::make('permissions.name')
                            ->label(__('filament.fields.direct_permissions'))
                            ->badge()
                            ->color('info')
                            ->placeholder(__('No direct permissions'))
                            ->columnSpanFull(),
                    ]),
                
                Section::make('Team Information')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('teams.name')
                            ->label(__('filament.fields.teams'))
                            ->badge()
                            ->color('primary')
                            ->placeholder(__('No teams'))
                            ->columnSpanFull(),
                        
                        TextEntry::make('currentTeam.name')
                            ->label(__('filament.fields.current_team'))
                            ->placeholder(__('No current team')),
                        
                        TextEntry::make('ownedTeams.name')
                            ->label(__('filament.fields.owned_teams'))
                            ->badge()
                            ->color('warning')
                            ->placeholder(__('No owned teams')),
                    ]),
                
                Section::make('Account Information')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(__('filament.fields.account_created'))
                            ->dateTime('M d, Y H:i')
                            ->icon('heroicon-o-calendar'),
                        
                        TextEntry::make('updated_at')
                            ->label(__('filament.fields.last_updated'))
                            ->dateTime('M d, Y H:i')
                            ->since()
                            ->icon('heroicon-o-clock'),
                        
                        TextEntry::make('two_factor_confirmed_at')
                            ->label(__('filament.fields.two_factor_enabled'))
                            ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No')
                            ->badge()
                            ->color(fn ($state) => $state ? 'success' : 'gray'),
                        
                        TextEntry::make('profile_photo_path')
                            ->label(__('filament.fields.profile_photo_path'))
                            ->placeholder(__('No custom photo'))
                            ->limit(50),
                    ]),
            ]);
    }
}
