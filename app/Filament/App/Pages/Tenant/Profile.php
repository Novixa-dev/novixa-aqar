<?php

namespace App\Filament\App\Pages\Tenant;

use App\Actions\Fortify\UpdateUserProfileInformation;
use Filament\Schemas\Schema;
use Filament\Pages\Page;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class Profile extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user';
    protected string $view = 'filament.app.tenant.profile';
    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'الملف الشخصي' : 'Profile';
    }

    public function getTitle(): string
    {
        return app()->getLocale() === 'ar' ? 'الملف الشخصي' : 'Profile';
    }

    public ?array $data = [];

    public function mount(): void
    {
        $user = Auth::user();
        $userData = $user->attributesToArray();
        $userData['average_rating'] = $user->averageRating();
        $this->form->fill($userData);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('filament.fields.name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label(__('filament.fields.email'))
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique('users', 'email', ignorable: fn () => Auth::user()),
                // Read-only: shown for information, never saved.
                TextInput::make('average_rating')
                    ->label(__('filament.fields.average_rating'))
                    ->disabled()
                    ->dehydrated(false)
                    ->afterStateHydrated(function (TextInput $component, $state) {
                        $component->state($state === null ? '—' : number_format((float) $state, 2));
                    }),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        // The same action as the account settings page, so an email change
        // follows one rule set everywhere.
        app(UpdateUserProfileInformation::class)->update(Auth::user(), [
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        Notification::make()
            ->success()
            ->title(app()->getLocale() === 'ar' ? 'تم تحديث الملف الشخصي بنجاح.' : 'Profile updated successfully.')
            ->send();
    }

    public static function getUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?\Illuminate\Database\Eloquent\Model $tenant = null, bool $shouldGuessMissingParameters = false, ?string $configuration = null): string
    {
        if ($tenant) {
            $parameters['tenant'] = $tenant;
        }
        return parent::getUrl($parameters, $isAbsolute, $panel, $tenant, $shouldGuessMissingParameters, $configuration);
    }

}
