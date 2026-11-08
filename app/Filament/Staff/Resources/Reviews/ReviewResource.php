<?php

namespace App\Filament\Staff\Resources\Reviews;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use App\Models\Property;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Staff\Resources\Reviews\Pages\ListReviews;
use App\Filament\Staff\Resources\Reviews\Pages\CreateReview;
use App\Filament\Staff\Resources\Reviews\Pages\EditReview;
use Filament\Forms;
use Filament\Tables;
use App\Models\Review;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

use App\Filament\Staff\Resources\ReviewResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Staff\Resources\ReviewResource\RelationManagers;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'تقييم' : 'Review';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'تقييمات' : 'Reviews';
    }

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                // `reviews.property_id` was dropped in favour of the polymorphic
                // reviewable_id/reviewable_type pair (migration
                // 2024_08_20_000000_update_reviews_table_for_polymorphic.php) —
                // Review no longer has a `property` relationship, only the
                // morphTo `reviewable()`. This form is scoped to property
                // reviews specifically (matches its previous property-only
                // intent), so reviewable_type is fixed to Property and only
                // reviewable_id needs picking.
                Select::make('reviewable_id')
                    ->label(__('filament.fields.property'))
                    ->options(fn () => Property::query()->pluck('title', 'id'))
                    ->searchable()
                    ->required(),
                Hidden::make('reviewable_type')
                    ->default(Property::class),
                TextInput::make('rating')
                    ->label(__('filament.fields.rating'))
                    ->required()
                    ->numeric()
                    ->min(1)
                    ->max(5),
                Textarea::make('comment')
                    ->label(__('filament.fields.comment'))
                    ->required(),
                DatePicker::make('review_date')
                    ->label(__('filament.fields.review_date'))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user_id')
                ->searchable()
                ->sortable(),
            TextColumn::make('reviewable.title')
                ->label(__('filament.fields.property'))
                ->searchable(),
            TextColumn::make('rating')
                ->searchable()
                ->sortable(),
            TextColumn::make('comment')
                ->searchable()
                ->sortable(),
            TextColumn::make('review_date')
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
            'index' => ListReviews::route('/'),
            'create' => CreateReview::route('/create'),
            'edit' => EditReview::route('/{record}/edit'),
        ];
    }
}
