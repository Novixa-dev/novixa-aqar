<?php

namespace App\Filament\Staff\Resources\Properties;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkAction;
use App\Filament\Staff\Resources\Properties\Pages\ListProperties;
use App\Filament\Staff\Resources\Properties\Pages\CreateProperty;
use App\Filament\Staff\Resources\Properties\Pages\EditProperty;
use App\Filament\Staff\Resources\PropertyResource\Pages;
use App\Models\Property;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;
use App\Filament\Staff\Resources\RelationManagers\ReviewsRelationManager;
use App\Filament\Staff\Resources\RelationManagers\RoomsRelationManager;
use Illuminate\Support\Collection;
use App\Filament\Forms\Components\FloorPlanEditor;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'عقار' : 'Property';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'عقارات' : 'Properties';
    }

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-office-2';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('property_template_id')
                    ->label(__('filament.fields.template'))
                    ->relationship('template', 'name')
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('content')
                            ->required()
                            ->label(__('filament.fields.template_content'))
                            ->helperText('Use placeholders like {title}, {description}, {price}, etc.')
                            ->rows(10),
                    ])
                    ->createOptionAction(function (Action $action) {
                        return $action->modalHeading('Create Property Template');
                    }),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),
                TextInput::make('location')
                    ->required()
                    ->maxLength(255),
                TextInput::make('price')
                    ->required()
                    ->numeric(),
                Select::make('currency')
                    ->label(__('filament.fields.currency'))
                    ->required()
                    ->default(config('currencies.default'))
                    ->options(app(\App\Services\CurrencyService::class)->options(app()->getLocale() === 'ar')),
                TextInput::make('bedrooms')
                    ->required()
                    ->numeric(),
                TextInput::make('bathrooms')
                    ->required()
                    ->numeric(),
                TextInput::make('area_sqft')
                    ->required()
                    ->numeric()
                    ->label(__('filament.fields.area_sqft')),
                TextInput::make('year_built')
                    ->required()
                    ->numeric()
                    ->minValue(1800)
                    ->maxValue(date('Y'))
                    ->label(__('filament.fields.year_built')),
                Select::make('property_type')
                    ->required()
                    ->options(fn () => app()->getLocale() === 'ar' ? [
                        'house' => 'منزل',
                        'apartment' => 'شقة',
                        'condo' => 'شقة سكنية',
                        'townhouse' => 'منزل متصل',
                    ] : [
                        'house' => 'House',
                        'apartment' => 'Apartment',
                        'condo' => 'Condo',
                        'townhouse' => 'Townhouse',
                    ]),
                Select::make('status')
                    ->required()
                    ->options(fn () => app()->getLocale() === 'ar' ? [
                        'for_sale' => 'للبيع',
                        'for_rent' => 'للإيجار',
                        'sold' => 'تم البيع',
                        'rented' => 'تم التأجير',
                    ] : [
                        'for_sale' => 'For Sale',
                        'for_rent' => 'For Rent',
                        'sold' => 'Sold',
                        'rented' => 'Rented',
                    ]),
                DatePicker::make('list_date')
                    ->required(),
                DatePicker::make('sold_date'),
                TextInput::make('virtual_tour_url')
                    ->url()
                    ->maxLength(255)
                    ->label(__('filament.fields.virtual_tour_url'))
                    ->helperText('Enter the URL for the virtual tour (Matterport, Kuula, etc.)'),
                Select::make('virtual_tour_provider')
                    ->options(fn () => [
                        'matterport' => 'Matterport',
                        'kuula' => 'Kuula',
                        '3d_vista' => '3D Vista',
                        'seekbeak' => 'Seekbeak',
                        'other' => app()->getLocale() === 'ar' ? 'أخرى' : 'Other',
                    ])
                    ->label(__('filament.fields.virtual_tour_provider'))
                    ->helperText('Select the provider of your virtual tour'),
                Textarea::make('virtual_tour_embed_code')
                    ->rows(3)
                    ->maxLength(2000)
                    ->label(__('filament.fields.virtual_tour_embed_code'))
                    ->helperText('Paste custom embed code if auto-generation from URL doesn\'t work'),
                Toggle::make('live_tour_available')
                    ->label(__('filament.fields.live_virtual_tours_available'))
                    ->helperText('Enable this to allow users to schedule live virtual tours with agents')
                    ->default(false),
                TextInput::make('model_3d_url')
                    ->label(__('filament.fields.3d_model_url'))
                    ->url()
                    ->maxLength(255)
                    ->helperText('URL to GLB/GLTF 3D model file'),
                Toggle::make('is_featured')
                    ->required(),
                Select::make('property_category_id')
                    ->relationship('category', 'name')
                    ->required()
                    ->label(__('filament.fields.property_category')),
                Select::make('energy_rating')
                    ->options([
                        'A' => 'A',
                        'B' => 'B',
                        'C' => 'C',
                        'D' => 'D',
                        'E' => 'E',
                        'F' => 'F',
                        'G' => 'G',
                    ])
                    ->label(__('filament.fields.energy_rating')),
                TextInput::make('energy_score')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->label(__('filament.fields.energy_score')),
                DatePicker::make('energy_rating_date')
                    ->label(__('filament.fields.energy_rating_date')),
                Repeater::make('features')
                    ->relationship()
                    ->schema([
                        TextInput::make('feature_name')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columnSpanFull(),
                SpatieMediaLibraryFileUpload::make('images')
                    ->collection('property_images')
                    ->multiple()
                    ->maxFiles(5)
                    ->label(__('filament.fields.property_images'))
                    ->columnSpanFull(),
                Textarea::make('custom_description')
                    ->label(__('filament.fields.custom_description'))
                    ->maxLength(1000),
                SpatieMediaLibraryFileUpload::make('video')
                    ->collection('videos')
                    ->maxFiles(1)
                    ->acceptedFileTypes(['video/mp4', 'video/quicktime'])
                    ->maxSize(102400), // 100MB
                SpatieMediaLibraryFileUpload::make('3d_model')
                    ->collection('3d_models')
                    ->maxFiles(1)
                    ->acceptedFileTypes(['model/gltf-binary', 'model/gltf+json', 'application/octet-stream'])
                    ->maxSize(51200) // 50MB
                    ->label(__('filament.fields.3d_model'))
                    ->helperText('Upload a 3D model file in GLB or GLTF format for interactive viewing')
                    ->columnSpanFull(),
                FloorPlanEditor::make('floor_plan_data')
                    ->label(__('filament.fields.interactive_floor_plan'))
                    ->columnSpanFull()
            ]);
    }
    
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('images')
                    ->collection('property_images')
                    ->label(__('filament.fields.preview'))
                    ->circular()
                    ->stacked()
                    ->limit(3),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('location')
                    ->searchable(),
                TextColumn::make('price')
                    ->formatStateUsing(fn (Property $record): string => $record->formatted_price)
                    ->sortable(),
                TextColumn::make('currency')
                    ->badge(),
                TextColumn::make('bedrooms')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('bathrooms')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('property_type')
                    ->searchable(),
                TextColumn::make('status')
                    ->searchable(),
                IconColumn::make('is_featured')
                    ->boolean(),
                TextColumn::make('custom_description')
                    ->label(__('filament.fields.custom_description'))
                    ->limit(50),
                IconColumn::make('has_video')
                    ->label(__('filament.fields.has_video'))
                    ->boolean()
                    ->trueIcon('heroicon-o-video-camera')
                    ->falseIcon('heroicon-o-x-circle')
                    ->getStateUsing(fn (Property $record): bool => $record->hasMedia('videos')),
                IconColumn::make('has_3d_model')
                    ->label(__('filament.fields.has_3d_model'))
                    ->boolean()
                    ->trueIcon('heroicon-o-cube')
                    ->falseIcon('heroicon-o-x-circle')
                    ->getStateUsing(fn (Property $record): bool => $record->hasMedia('3d_models')),
            ])
            ->filters([
                SelectFilter::make('property_type'),
                SelectFilter::make('status'),
                Filter::make('is_featured')
                    ->query(fn (Builder $query): Builder => $query->where('is_featured', true)),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('updateFeatured')
                        ->label(__('filament.fields.update_featured_status'))
                        ->action(function (Collection $records, array $data): void {
                            $records->each(function ($record) use ($data) {
                                $record->update(['is_featured' => $data['is_featured']]);
                            });
                        })
                        ->schema([
                            Toggle::make('is_featured')
                                ->label(__('filament.fields.featured'))
                                ->required(),
                        ]),
                ]),
            ]);
    }

public static function getRelations(): array
{
    return [
        ReviewsRelationManager::class,
        RoomsRelationManager::class,
    ];
}
public static function getPages(): array
{
    return [
        'index' => ListProperties::route('/'),
        'create' => CreateProperty::route('/create'),
        'edit' => EditProperty::route('/{record}/edit'),
    ];
}
public static function canViewRelation(string $relationName, $record): bool
{
    if ($relationName === 'rooms') {
        return $record->isHmo();
    }
    return true;
}
}
