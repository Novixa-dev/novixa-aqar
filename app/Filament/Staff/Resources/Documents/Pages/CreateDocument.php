<?php

namespace App\Filament\Staff\Resources\Documents\Pages;

use App\Filament\Staff\Resources\Documents\DocumentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDocument extends CreateRecord
{
    protected static string $resource = DocumentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        if (!empty($data['file_path'])) {
            $disk = \Illuminate\Support\Facades\Storage::disk('public');

            $data['size'] = $disk->size($data['file_path']);
            $ext = strtolower(pathinfo($data['file_path'], PATHINFO_EXTENSION));
            $data['file_type'] = match ($ext) {
                'pdf' => 'pdf',
                'doc' => 'doc',
                'docx' => 'docx',
                default => $ext,
            };
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}