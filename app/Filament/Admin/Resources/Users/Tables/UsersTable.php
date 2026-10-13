<?php

namespace App\Filament\Admin\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('profile_photo_url')
                    ->label(__('filament.fields.photo'))
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=7F9CF5&background=EBF4FF'),
                
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn ($record) => $record->email, position: 'below'),
                
                TextColumn::make('roles.name')
                    ->label(__('filament.fields.roles'))
                    ->badge()
                    ->formatStateUsing(\App\Support\StatusLabels::for('role'))
                    ->color('success')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->placeholder(__('No roles assigned')),
                
                IconColumn::make('email_verified_at')
                    ->label(__('filament.fields.verified'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable()
                    ->tooltip(fn ($record) => $record->email_verified_at 
                        ? 'Verified on ' . $record->email_verified_at->format('M d, Y') 
                        : 'Not verified'),
                
                TextColumn::make('teams_count')
                    ->label(__('filament.fields.teams'))
                    ->counts('teams')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                
                TextColumn::make('created_at')
                    ->label(__('filament.fields.joined'))
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->label(__('filament.fields.last_updated'))
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record): string => \App\Support\StatusLabels::label('role', $record->name))
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->placeholder(__('Filter by role')),
                
                Filter::make('verified')
                    ->label(__('filament.fields.email_verified'))
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at')),
                
                Filter::make('unverified')
                    ->label(__('filament.fields.email_unverified'))
                    ->query(fn (Builder $query): Builder => $query->whereNull('email_verified_at')),
                
                Filter::make('created_at')
                    ->label(__('filament.fields.recently_joined'))
                    ->query(fn (Builder $query): Builder => $query->where('created_at', '>=', now()->subDays(30))),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
