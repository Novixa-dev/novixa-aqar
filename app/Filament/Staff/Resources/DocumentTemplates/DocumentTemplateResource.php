<?php

namespace App\Filament\Staff\Resources\DocumentTemplates;

use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Staff\Resources\DocumentTemplates\Pages\ListDocumentTemplates;
use App\Filament\Staff\Resources\DocumentTemplates\Pages\CreateDocumentTemplate;
use App\Filament\Staff\Resources\DocumentTemplates\Pages\EditDocumentTemplate;
use App\Models\DocumentTemplate;
use Filament\Forms;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use App\Filament\Staff\Resources\DocumentTemplateResource\Pages;

class DocumentTemplateResource extends Resource
{
    protected static ?string $model = DocumentTemplate::class;

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'قالب مستند' : 'Document Template';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'قوالب مستندات' : 'Document Templates';
    }

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->label(__('filament.fields.template_name')),
                FileUpload::make('file_path')
                    ->required()
                    ->label(__('filament.fields.template_file'))
                    ->disk('public')
                    ->directory('document_templates')
                    ->acceptedFileTypes(['.doc', '.docx', '.pdf']),
                Textarea::make('description')
                    ->label(__('filament.fields.description')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('filament.fields.name')),
                TextColumn::make('file_path')->label(__('filament.fields.file_path')),
                TextColumn::make('description')->label(__('filament.fields.description')),
            ])
            ->filters([
                //
            ]);
    }

    public static function getNavigationGroup(): string | \UnitEnum | null
    {
        return \App\Filament\Support\NavigationGroup::Administration;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDocumentTemplates::route('/'),
            'create' => CreateDocumentTemplate::route('/create'),
            'edit' => EditDocumentTemplate::route('/{record}/edit'),
        ];
    }
}
