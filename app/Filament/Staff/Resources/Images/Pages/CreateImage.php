<?php

namespace App\Filament\Staff\Resources\Images\Pages;

use App\Filament\Staff\Resources\Images\ImageResource;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateImage extends CreateRecord
{
    protected static string $resource = ImageResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        if (!empty($data['file_path'])) {
            $disk = \Illuminate\Support\Facades\Storage::disk('public');
            $full = $disk->path($data['file_path']);

            $data['disk'] = 'public';
            $data['file_name'] = basename($data['file_path']);

            if (file_exists($full)) {
                [$width, $height] = getimagesize($full) ?: [null, null];
                $data['mime_type'] = mime_content_type($full);
                $data['file_size'] = filesize($full);

                if ($width && $height) {
                    $data['metadata'] = array_merge(($data['metadata'] ?? []), [
                        'width' => $width,
                        'height' => $height,
                    ]);
                }
            }
        }

        return $data;
    }
}
